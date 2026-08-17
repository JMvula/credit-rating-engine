<?php
// ApiClient.php (identical to the one in credit-gateway)

class ApiClient {

    private $baseUrl;
    private $apiKey;
    private $timeout;

    public function __construct($baseUrl, $apiKey = null, $timeout = 30) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey  = $apiKey;
        $this->timeout = $timeout;
    }

    private function request($method, $endpoint, $data = null) {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $ch  = curl_init();

        $headers = ['Content-Type: application/json', 'Accept: application/json'];
        if ($this->apiKey) {
            $headers[] = 'Authorization: Bearer ' . $this->apiKey;
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_CUSTOMREQUEST  => $method,
        ]);

        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'error' => 'Network error: ' . $curlError, 'status' => 0, 'data' => null];
        }

        $decoded = json_decode($response, true);
       
    
        $success = $httpCode >= 200 && $httpCode < 300;

        if (!$success) {
            $errorMessages = [
                400 => 'Bad Request', 401 => 'Unauthorised', 404 => 'Not Found',
                429 => 'Rate Limited', 500 => 'Server Error', 502 => 'Bad Gateway',
                503 => 'Service Unavailable',
            ];
            $message = $errorMessages[$httpCode] ?? 'Unknown error (HTTP ' . $httpCode . ')';
            return ['success' => false, 'error' => $message, 'status' => $httpCode, 'data' => $decoded];
        }

        return ['success' => true, 'error' => null, 'status' => $httpCode, 'data' => $decoded];
    }

    public function get($endpoint)          { return $this->request('GET',    $endpoint); }
    public function post($endpoint, $data)  { return $this->request('POST',   $endpoint, $data); }
    public function put($endpoint, $data)   { return $this->request('PUT',    $endpoint, $data); }
    public function patch($endpoint, $data) { return $this->request('PATCH',  $endpoint, $data); }
    public function delete($endpoint)       { return $this->request('DELETE', $endpoint); }
}