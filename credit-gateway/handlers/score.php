<?php
// handlers/score.php
// Handles POST /api/score — forwards applicant data to the Python model
// service and relays the result back, unchanged in shape.

function handleScore($method) {

    // 1. Only POST makes sense for scoring an applicant
    if ($method !== 'POST') {
        respond(['error' => 'Method not allowed. Use POST.'], 405);
    }

    // 2. Read and sanity-check the request body.
    //    Notice: we check that it's valid, non-empty JSON — but we do NOT
    //    check for specific field names. That validation belongs to Flask,
    //    which is the authority on the model's feature contract.
    $rawBody = file_get_contents('php://input');
    $applicantData = json_decode($rawBody, true);

    if (json_last_error() !== JSON_ERROR_NONE || empty($applicantData)) {
        respond(['error' => 'Request body must be non-empty valid JSON'], 400);
    }

    // 3. Forward it to the Python model service, exactly as received
    $client = new ApiClient(PYTHON_SERVICE_URL, null, PYTHON_SERVICE_TIMEOUT);
    $result = $client->post('/v1/predict', $applicantData);

    // 4. Network-level failure — Flask isn't running, wrong host/port, etc.
    //    This is a DIFFERENT problem from "the applicant's data was bad",
    //    so it gets its own status code: 502 Bad Gateway, the correct code
    //    for "I tried to reach an upstream service and couldn't."
    if ($result['status'] === 0) {
        respond([
            'error'  => 'Could not reach the credit scoring service',
            'detail' => $result['error']
        ], 502);
    }

    // 5. Flask reached us fine, but said no (e.g. 400 missing fields,
    //    500 prediction error). Relay its message and status AS-IS —
    //    we don't rewrite it, because Flask's config.py knows the real
    //    reason, not us.
    if (!$result['success']) {
        respond([
            'error'  => $result['error'],
            'detail' => $result['data']
        ], $result['status']);
    }

    // 6. Success — wrap the prediction in a small, consistent envelope
    //    for the frontend and send it back.
    respond([
        'success'    => true,
        'prediction' => $result['data']
    ], 200);
}