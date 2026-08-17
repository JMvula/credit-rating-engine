<?php
// controllers/ScoreController.php

class ScoreController {

    private $gateway;

    public function __construct() {
        $this->gateway = new ScoringGatewayClient();
    }

    // GET / -> show the empty form
    public function showForm() {
        $viewData = [
            'result' => null,
            'error'  => null,
        ];
        require 'views/form.php';
    }

    // POST / -> process submission, call the gateway, show the result
    public function submitForm(array $postData) {
        $applicantData = $this->castNumericStrings($postData);

        $response = $this->gateway->score($applicantData);


        $viewData = [
            'result' => null,
            'error'  => null,
        ];

        if ($response['status'] === 0) {
            // The gateway itself is unreachable (not the same as the gateway
            // saying Flask is unreachable — this is one hop further out)
            $viewData['error'] = 'Could not reach the scoring gateway. Is it running?';
        } elseif (!$response['success']) {
            // The gateway responded, but with an error — relay its message
            $viewData['error'] = $response['error'] ?? 'Something went wrong.';
        } else {
            // Success — the gateway wraps it as {"success": true, "prediction": {...}}
            $viewData['result'] = $response['data']['prediction'] ?? null;
        }

        require 'views/result.php';
    }

    // HTML forms submit everything as strings. This turns numeric-looking
    // strings ("1500") into real PHP numbers, so they reach the gateway as
    // JSON numbers, not JSON strings. Anything non-numeric (e.g. a future
    // "employment_type" field) is left exactly as it was.
    private function castNumericStrings(array $data): array {
        foreach ($data as $key => $value) {
            if (is_string($value) && is_numeric($value)) {
                $data[$key] = $value + 0;
            }
        }
        return $data;
    }
}