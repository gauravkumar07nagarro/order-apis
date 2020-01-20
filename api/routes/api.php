<?php

use Illuminate\Http\Response;

$api = app('Dingo\Api\Routing\Router');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {
        $api->get('orders',         'ApiController@index')->name('orders.index');
        $api->post('orders',        'ApiController@store')->name('orders.store');
        $api->patch('orders/{id}',  'ApiController@update')->name('orders.patch');
    });
});

app('Dingo\Api\Exception\Handler')->register(function (\Exception $exception) {
    if ($exception->getStatusCode() == Response::HTTP_NOT_FOUND ) {
        $error = ["error" => trans('order.bad_request')];
    } else {
        $error = ["error" =>  $exception->getMessage()];
    }
    return response()->json($error, $exception->getStatusCode());
});

