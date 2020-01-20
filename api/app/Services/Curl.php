<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class Curl
{
    protected $client = null;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getRequest($url = null, $params = [])
    {

        $response = null;
        try {
            $request = $this->client->request('GET', $url, [
                'query' => $params
            ]);

            $response = $request->getBody();
        } catch (ClientException $exception) {
            Log::error("Curl Exception :: ". json_encode($exception));
            throw new \Exception($exception->getMessage());
        }

        if ( $response != null ) {
            return json_decode($response, true);
        } else {
            return $response;
        }

    }

}
