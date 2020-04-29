<?php

namespace GiataHotels;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class API
{
    public static function getHotelsByCountry($country, $multi = false, $offset = false)
    {
        $baseUrl = $multi ? config('giata-api.multicodes.countryMultiUrl') : config('giata-api.multicodes.countryUrl');
        $url = $baseUrl . ($offset ? $country . '/offset/' . $offset : $country);
        return self::callGiataAPI($url);
    }

    public static function getHotelByGiataId($giataId)
    {
        $baseUrl = config('giata-api.multicodes.giataIdUrl');
        $url = $baseUrl . $giataId;
        return self::callGiataAPI($url);
    }

    public static function getImagesByGiataId($giataId)
    {
        $baseUrl = config('giata-api.ghgml.imagesUrl');
        $url = $baseUrl . $giataId;
        return self::callGiataAPI($url);
    }

    public static function getTextsByGiataId($giataId, $lang = 'ar')
    {
        $baseUrl = config('giata-api.ghgml.textUrl');
        $url = $baseUrl . $lang . '/' . $giataId;
        return self::callGiataAPI($url);
    }

    protected static function callGiataAPI($url)
    {
        $client = new Client([
            'auth' => [config('giata-api.username'), config('giata-api.password')],
            'timeout' => config('giata-api.timeout', 120),
            'headers' => ['Content-Type' => 'text/xml',]
        ]);
        try {
            $response = $client->post($url);
            $body = $response->getBody()->getContents();
            return xmlToArray::convert($body);
        } catch (GuzzleException $e) {
            return ['status' => 500, 'error' => $e->getMessage()];
        }
    }
}
