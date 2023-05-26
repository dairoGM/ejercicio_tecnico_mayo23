<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Service("agent.utils")
 */
class Utils
{
    private $baseUrl;

    public function __construct(RequestStack $requestStack)
    {
        $this->baseUrl = $requestStack->getCurrentRequest()->getSchemeAndHttpHost();
    }

    public function getToken($email, $password)
    {
        $httpClient = new \GuzzleHttp\Client();
        $response = $httpClient->request('POST', $this->baseUrl . "/api/login_check", [
            'body' => json_encode(['email' => $email, 'password' => $password]),
            'headers'  => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $result = json_decode($response->getBody()->getContents(), true);
        $token = isset($result['token']) ? $result['token'] : null;
        return $token;
    }


    public function getToken1($email, $password)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . "/api/login_check",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{"email":"' . $email . '","password":"' . $password . '"}',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Basic"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $token = json_decode($response, true);
        $token = isset($token['token']) ? $token['token'] : null;
        return $token;
    }
}
