<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Order Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during order operations like creation, updation, listing etc..
    |
    */

    'invalid_origin_lat' => 'Invalid Orgin lattitude value, please enter valid lattitude value',
    'invalid_origin_long' => 'Invalid Origin longitude value, please enter valid longitude value',

    'invalid_destination_lat' => 'Invalid Destination lattitude value, please enter valid lattitude value',
    'invalid_destination_long' => 'Invalid Destination longitude value, please enter valid longitude value',

    'invalid_origin_lat_long' => 'Invalid Origin Lat & Long values, please enter valid Lat & Long as a string',
    'invalid_destination_lat_long' => 'Invalid Destination Lat & Long values, please enter valid Lat & Long as a string',


    'invalid_distance' => 'Invalid location, please enter valid location or check google api',
    'orders_not_found'  => 'Order not found or it has been taken!',
    'update_success'   =>  'SUCCESS',
    'update_failure' => 'Invalid order or status has been changed',
    'content_type_failure' => 'Content-type must be application/json ',
    'google_api_exception' => 'Please enter a valid google api key',
    'google_api_distance_exception' => 'Distance cannot be calculated with selected locations',
    'bad_request' => 'Bad request, please check the endpoint',
    'same_origin_and_destination' => 'origin and destination cannot be same'
];
