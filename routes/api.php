<?php

namespace App\Routes;

use App\AppRouter;

class Api
{
    public static function register($router)
    {
        AppRouter::get('/users', 'UserController@index');
        AppRouter::get('/users/{id}', 'UserController@show');
        AppRouter::post('/users', 'UserController@store');
        AppRouter::put('/users/{id}', 'UserController@update');
        AppRouter::delete('/users/{id}', 'UserController@delete');
    }
}
