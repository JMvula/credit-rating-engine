<?php
// models/ScoringGatewayClient.php
// The frontend's Model layer. Knows how to reach the gateway.
// Does NOT know the feature names — it just forwards whatever it's given.

class ScoringGatewayClient {

    private $client;

    public function __construct() {
        $this->client = new ApiClient(GATEWAY_URL, null, GATEWAY_TIMEOUT);
    }

    public function score(array $applicantData): array {
        return $this->client->post('/api/score', $applicantData);
    }
}