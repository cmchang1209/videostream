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


Route::get('/getEquipmentListData', 'ApiEquipmentController@getListData')->name('getEquipmentListData');
Route::get('/getDistributorData', 'ApiEquipmentController@getDistributorData')->name('getDistributorData');
Route::get('/getStoreData', 'ApiEquipmentController@getStoreData')->name('getStoreData');
Route::post('/addEquipment', 'ApiEquipmentController@add')->name('addEquipment');
Route::get('/getEquipment', 'ApiEquipmentController@getData')->name('getEquipment');
Route::post('/updateEquipment', 'ApiEquipmentController@update')->name('updateEquipment');
Route::delete('/deleteEquipment', 'ApiEquipmentController@delete')->name('deleteEquipment');
Route::get('/getEquipmentStatus', 'ApiEquipmentController@getStatus')->name('getEquipmentStatus');
Route::get('/getEquipmentPort', 'ApiEquipmentController@getPort')->name('getEquipmentPort');
Route::post('/setAudio', 'ApiEquipmentController@setAudio')->name('setAudio');




Route::get('/getTournamentListData', 'ApiTournamentController@getTournamentListData')->name('getTournamentListData');
Route::post('/addTournament', 'ApiTournamentController@addTournament')->name('addTournament');
Route::post('/updateTournament', 'ApiTournamentController@updateTournament')->name('updateTournament');
Route::delete('/deleteTournament', 'ApiTournamentController@deleteTournament')->name('deleteTournament');
Route::get('/getPlayersData', 'ApiTournamentController@getPlayersData')->name('getPlayersData');
Route::get('/getEquipmentData', 'ApiTournamentController@getEquipmentData')->name('getEquipmentData');
Route::get('/getTournamentData', 'ApiTournamentController@getTournamentData')->name('getTournamentData');
Route::get('/getTournamentBracketData', 'ApiTournamentController@getTournamentBracketData')->name('getTournamentBracketData');


Route::get('/getLeagueListData', 'ApiLeagueController@getLeagueListData')->name('getLeagueListData');
Route::get('/getLeagueGroupData', 'ApiLeagueController@getLeagueGroupData')->name('getLeagueGroupData');
Route::get('/getLeagueBattleData', 'ApiLeagueController@getLeagueBattleData')->name('getLeagueBattleData');
Route::post('/updateLeaguePiData', 'ApiLeagueController@updateLeaguePiData')->name('updateLeaguePiData');
Route::get('/getLgViewData', 'ApiLeagueController@getLgViewData')->name('getLgViewData');
