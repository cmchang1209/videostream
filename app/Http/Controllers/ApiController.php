<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
	public function login(Request $request)
    {
    	$data = [];
    	$data['errorCode'] = 'er0000';
    	$pass = md5($request->pass);
    	$sql = 'SELECT id, roleCode, roleID FROM privilege_user WHERE email=:email AND password=:password AND isBaned=0 AND roleCode<=2';
    	$data['data'] = DB::connection('mysql')->select($sql, ['email' => $request->account, 'password' => $pass]);
    	if(count($data['data'])) {
    		$id = base64_encode($data['data'][0]->id.','.$data['data'][0]->roleID.','.$data['data'][0]->roleCode);
    		$data['data'] = $id;
    	} else {
    		$data['errorCode'] = 'er0003';
    	}
    	return compact('data');
    }

    public function getTournamentData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $sql = 'SELECT id, fidoID, name, nickName FROM users';
        $players = DB::connection('mysql')->select($sql);
        $filtered = [];
        foreach ($players as $key => $item) {
            /*if(strpos($item->fidoID, $request->name) !== FALSE || strpos(base64_decode($item->name), $request->name) !== FALSE || strpos(base64_decode($item->nickName), $request->name)){
                array_push($filtered, $item);
            }*/
            array_push($filtered, $item);
        }
        /*$players = collect($players);
        $filtered = $players->filter(function ($item) use ($request) {
            return (strpos($item->fidoID, $request->name) !== FALSE || strpos(base64_decode($item->name), $request->name) !== FALSE || strpos(base64_decode($item->nickName), $request->name));
        });*/
        $data['players'] = $filtered;
        return compact('data');
    }

}