<?php

use Illuminate\Http\Request;

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

Route::post('/login', 'ApiController@login')->name('login');

Route::get('/getTournamentListData', 'ApiController@getTournamentListData')->name('getTournamentListData');
Route::post('/addTournament', 'ApiController@addTournament')->name('addTournament');
Route::post('/updateTournament', 'ApiController@updateTournament')->name('updateTournament');
Route::delete('/deleteTournament', 'ApiController@deleteTournament')->name('deleteTournament');
Route::get('/getPlayersData', 'ApiController@getPlayersData')->name('getPlayersData');
Route::get('/getEquipmentData', 'ApiController@getEquipmentData')->name('getEquipmentData');
Route::get('/getTournamentData', 'ApiController@getTournamentData')->name('getTournamentData');
