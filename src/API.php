<?php

namespace GiataHotels;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class API
{
    public function getHotels($country)
    {
        $baseUrl = config('giata-api.multicodes.countryUrl');
        $url = $baseUrl . $country;
        return $this->sendRequest($url);
    }

    public function getHotel($giataId)
    {
        $baseUrl = config('giata-api.multicodes.giataIdUrl');
        $url = $baseUrl . $giataId;
        return $this->sendRequest($url);
    }

    public function getImages($giataId)
    {
        $baseUrl = config('giata-api.ghgml.imagesUrl');
        $url = $baseUrl . $giataId;
        return $this->sendRequest($url);
    }

    public function getTexts($giataId, $lang = 'ar')
    {
        $baseUrl = config('giata-api.ghgml.textUrl');
        $url = $baseUrl . $lang . '/' . $giataId;
        return $this->sendRequest($url);
    }

    private function sendRequest($url)
    {
        $client = new Client([
            'auth' => [config('giata-api.username'), config('giata-api.password')],
            'timeout' => config('giata-api.timeout', 120),
        ]);
        try {
            $res = $client->post($url);
            return response()->json([
                'code' => $res->getStatusCode(),
                'message' => $res->getReasonPhrase(),
                'data' => $res->getBody()->getContents()
            ]);
        } catch (GuzzleException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
