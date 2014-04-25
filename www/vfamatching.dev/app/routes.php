<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('login', array('as' => 'login', function () { return View::make('login'); }))->before('guest');
Route::post('login', 'UsersController@login');
Route::get('users/password-reset/{hash}', array('uses' => 'UsersController@newPassword'));
Route::post('users/password-reset', array('uses' => 'UsersController@updatePassword'));
Route::get('logout', array('uses' => 'UsersController@logout', 'as' => 'logout'))->before('auth');

Route::group(array('before' => 'auth'), function()
{
    /* These routes are available to users without profiles */
    Route::get('fellows/create', array('uses' => 'FellowsController@create'));
    Route::post('fellows', array('uses' => 'FellowsController@store'));
    Route::get('hiringmanagers/create', array('uses' => 'HiringManagersController@create'));
    Route::post('hiringmanagers', array('uses' => 'HiringManagersController@store'));
    Route::get('hiringmanagers/{id}/edit', array('uses' => 'HiringManagersController@edit'));
    Route::put('hiringmanagers/{id}', array('uses' => 'HiringManagersController@update'));
    Route::get('companies/{id}/edit', array('uses' => 'CompaniesController@edit'));
    Route::put('companies/{id}', array('uses' => 'CompaniesController@update'));
    Route::get('opportunities/create', array('uses' => 'OpportunitiesController@create'));
    Route::post('opportunities', array('uses' => 'OpportunitiesController@store'));
    Route::get('admins/create', array('uses' => 'AdminsController@create'));
    Route::post('admins', array('uses' => 'AdminsController@store'));
    Route::group(array('before' => 'profile'), function() /* Requires a profile to be created before using the site */
    {
        Route::get('/', array('as' => 'dashboard', 'uses' => 'UsersController@dashboard'));
        Route::resource('users', 'UsersController');
        Route::resource('companies', 'CompaniesController');
        Route::resource('medialinks', 'MediaLinksController');
        Route::resource('fellows', 'FellowsController');
        Route::resource('opportunities', 'OpportunitiesController');
        Route::resource('hiringmanagers', 'HiringManagersController');
        Route::resource('admins', 'AdminsController');
        Route::resource('placementstatuses', 'PlacementStatusesController');
        Route::resource('adminnotes', 'AdminNotesController');
        Route::resource('fellownotes', 'FellowNotesController');
        Route::resource('pitches', 'PitchesController');
        Route::resource('fellowskills', 'FellowSkillsController');
        Route::resource('opportunitytags', 'OpportunityTagsController');
        Route::resource('staffrecommendations', 'StaffRecommendationsController');
        Route::resource('pitchinvites', 'PitchInvitesController');
        Route::put('opportunities/{id}/publish', 'OpportunitiesController@publish');
        Route::put('opportunities/{id}/unpublish', 'OpportunitiesController@unpublish');
        Route::put('pitches/{id}/approve', 'PitchesController@approve');
        Route::put('pitches/{id}/waitlist', 'PitchesController@waitlist');
        Route::get('reports/{type}', array('uses'=>'ReportsController@show'));
        Route::group(array('before' => 'admin'), function() /* Requires the user to be an admin */
        {
            Route::get('users/{id}/backdoor', array('uses' => 'UsersController@backdoor'));
            Route::put('fellows/{id}/publish', 'FellowsController@publish');
            Route::put('fellows/{id}/unpublish', 'FellowsController@unpublish');
            Route::put('companies/{id}/publish', 'CompaniesController@publish');
            Route::put('companies/{id}/unpublish', 'CompaniesController@unpublish');
            Route::get('archive', array('as'=>'archive', 'uses'=>'AdminsController@archive'));
            Route::get('reports', array('as'=>'reports', 'uses'=>'ReportsController@index'));
            Route::get('users/{id}/password-reset', array('uses' => 'UsersController@resetPassword'));
        });
    });
});

