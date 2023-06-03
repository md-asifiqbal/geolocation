<?php

namespace App\Services;

class GeolocationService
{
    protected $api_key;
    public function __construct()
    {
        $this->api_key = env('GOOGLE_MAP_API_KEY', 'AIzaSyBxdpoBaLfWkQhHsaVdZt8zOGJ_58GWmiU');
    }


    public function getNearbyPlace($lat, $lng, $radius)
    {
        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$lat,$lng&radius=$radius&key=" .  $this->api_key;
        $response = $this->callApi($url);
        return collect(json_decode($response)->results);
    }

    public function getPlaceDetails($lat, $lng)
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&key=" .  $this->api_key;
        $response = $this->callApi($url);
        if (!isset(json_decode($response)?->results[0]?->place_id)) {
            throw new \Exception('Place not found', 404);
        }
        $place_id = json_decode($response)->results[0]->place_id;
        $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id=$place_id&location=$lat,$lng&key=" .  $this->api_key;
        $response = $this->callApi($url);
        return json_decode($response)->result;
    }

    public function getPlaceDetailsByPlaceId($place_id)
    {
        $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id=$place_id&key=" .  $this->api_key;
        $response = $this->callApi($url);
        return json_decode($response)->result;
    }

    public function callApi($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
