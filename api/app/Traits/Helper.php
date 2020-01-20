<?php

/**
 * Custom helper class used for multipurpose
 *
 * @author Gaurav Kumar<gaurav.kumar07@nagarro.com>
 */

namespace App\Traits;
use App\Services\Curl;
use Illuminate\Http\Response;

trait Helper {
    /**
     * Function used to parse the response from google distance matrix api
     *
     * @return int
     */
    public function calculateDistanceBetweenOriginAndDestination($origin, $destination)
    {

        try {
            $curl = new Curl();
            $totalDistance = null;

            $url = config('services.google_map.distance_api');
            $params = [
                'origins'       => $origin,
                'destinations'  => $destination,
                'key'           => config('services.google_map.api_key')
            ];

            $response = $curl->getRequest($url, $params);

            if (!empty($response) && isset($response['rows'][0]['elements'][0]['distance']['value'])) {
                $totalDistance = $response['rows'][0]['elements'][0]['distance']['value'];
            }elseif(!empty($response) && isset($response['rows'][0]['elements'][0]['status'])){
                throw new \Exception(trans('order.google_api_distance_exception'));
            }

            if ( $totalDistance == null ) {
                throw new \Exception(trans('order.google_api_exception'));
            }

            return $totalDistance;
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function apiSuccessResponse($data = [], $headerStatusCode = Response::HTTP_OK, $headers = [])
    {
        return response()->json($data, $headerStatusCode);
    }


    public function apiErrorResponse($error, $headerStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY, $headers = [])
    {
        return response()->json(['error' => $error], $headerStatusCode);
    }


    public function apiExceptionResponse($exception)
    {
        return response()->json(['error' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
    }


}
