<?php

class MamlakaAPI
{
    private $baseUrl;
    private $token;
    private $tokenBase64;

    public function __construct($environment = 'development')
    {
        $this->baseUrl = $environment === 'production'
            ? 'https://official.mam-laka.com'
            : 'http://staging.mam-laka.com';
    }

    /**
     * Authenticate and generate a Base64-encoded token.
     * @param string $username
     * @param string $password
     * @throws Exception
     */
    public function getToken($username, $password)
    {
        $url = $this->baseUrl . '/api/';
        $headers = [
            "Authorization: Basic " . base64_encode("$username:$password"),
        ];

        $response = $this->makeRequest('GET', $url, null, $headers);
        // print_r($response);

        if ($response['status'] === 200 && isset($response['body']['accessToken'])) {
            $this->token = $response['body']['accessToken'];
            $this->tokenBase64 = base64_encode($this->token);
            return array('error' =>false, 'message' => 'Authentication successful', "token" => $this->tokenBase64);
        
        } else {
            return array('error' => true, 'message' => 'Authentication failed');
            // throw new Exception('Authentication failed: ' . $response['body']['message'] ?? 'Unknown error');
        }
    }

    /**
     * Initiate a card payment.
     * @param string $merchantId
     * @param string $currency
     * @param float $amount
     * @param string $externalId
     * @param string $callbackUrl
     * @param string $redirectUrl
     * @return array
     * @throws Exception
     */
    public function initiateCardPayment($merchantId, $currency, $amount, $externalId, $callbackUrl, $redirectUrl)
    {
        $endpoint = '/api/?resource=merchant&action=generateCardPaymentLink';
        $data = [
            "impalaMerchantId" => $merchantId,
            "currency" => $currency,
            "amount" => $amount,
            "externalId" => $externalId,
            "callbackUrl" => $callbackUrl,
            "redirectUrl" => $redirectUrl,
        ];

        return $this->callAPI('POST', $endpoint, $data);
    }

    /**
     * Initiate a mobile payment.
     * @param string $merchantId
     * @param string $currency
     * @param float $amount
     * @param string $payerPhone
     * @param string $mobileMoneySP
     * @param string $externalId
     * @param string $callbackUrl
     * @return array
     * @throws Exception
     */
    public function initiateMobilePayment($merchantId, $currency, $amount, $payerPhone, $mobileMoneySP, $externalId, $callbackUrl)
    {
        $endpoint = '/api/?resource=merchant&action=initiate_mobile_payment';
        $data = [
            "impalaMerchantId" => $merchantId,
            "currency" => $currency,
            "amount" => $amount,
            "payerPhone" => $payerPhone,
            "mobileMoneySP" => $mobileMoneySP,
            "externalId" => $externalId,
            "callbackUrl" => $callbackUrl,
        ];

        return $this->callAPI('POST', $endpoint, $data);
    }
    /**
     * Make an HTTP request using cURL.
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $url API endpoint
     * @param array|null $data Request body (optional)
     * @param array $headers HTTP headers
     * @return array
     */
    private function makeRequest($method, $url, $data = null, $headers = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            throw new Exception("cURL Error: $curlError");
        }

        return [
            'status' => $httpCode,
            'body' => json_decode($response, true),
        ];
    }

    /**
     * Make an API request.
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array|null $data Request body
     * @return array
     * @throws Exception
     */
    private function callAPI($method, $endpoint, $data = null)
    {
        if (!$this->tokenBase64) {
            throw new Exception('Token not set. Authenticate first.');
        }

        $url = $this->baseUrl . $endpoint;

        $headers = [
            "Authorization: Bearer {$this->tokenBase64}",
            "Content-Type: application/json",
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            throw new Exception("API call failed: HTTP {$httpCode} - $response");
        }

        return json_decode($response, true);
    }
}

// Example Usage
try {
    $api = new MamlakaAPI('production'); // Switch to 'development' as needed
    $username = "CometAppMain";
    $password = "cometappmain";
    $response = $api->getToken($username, $password);
    if(!$response['error']){
        $mobilePayment = $api->initiateMobilePayment(
            'Test1aBCDEFGHIJKLMmamlaka',
            'KES',
            1,
            '254768899729',
            'M-Pesa',
            'ext67890169',
            'https://b8ca-217-21-116-242.ngrok-free.app'
        );
        print_r($mobilePayment);
        
    } else {
        print("invalid credentials");
    }
    // print_r($response);

    // // Card Payment
    // $cardPayment = $api->initiateCardPayment(
    //     'merchantId',
    //     'USD',
    //     100.50,
    //     'ext12345',
    //     'https://b8ca-217-21-116-242.ngrok-free.app',
    //     'https://b8ca-217-21-116-242.ngrok-free.app'
    // );
    // print_r($cardPayment);

    // // Mobile Payment
    

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}