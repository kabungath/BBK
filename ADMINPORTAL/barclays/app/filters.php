<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (!Sentry::check())
	{
		return Redirect::to('login');
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

Route::filter('auth.token', function($route, $request)
{
    $payload = $request->header('X-Auth-Token');
    $userModel = Sentry::getUserProvider()->createModel();
    $user =  $userModel->where('api_token',$payload)->first();

    if(!$payload || !$user) {

        $response = Response::json([
            'error' => true,
            'message' => 'Not authenticated',
            'code' => 401],
            401
        );

        $response->header('Content-Type', 'application/json');
    return $response;
    }

});

Route::filter('public.token', function($route, $request)
{
    $payload = $request->header('X-Auth-Token');
    $server_token="0e0624befccc6c1980859ce59828eedf9bc277683dcf9bb9eda827e342620894";
   

    if($payload !=$server_token) {

        $response = Response::json([
            'error' => true,
            'message' => 'Not authenticated',
            'code' => 401],
            401
        );

        $response->header('Content-Type', 'application/json');
    return $response;
    }

});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
