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

    public function getPlayersData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $data['players'] = [];
        $sql = 'SELECT id, fidoID, name, nickName FROM users';
        $players = DB::connection('mysql')->select($sql);
        foreach ($players as $key => $value) {
            $name = base64_encode(base64_decode($value->name)) === $value->name? base64_decode($value->name) : $value->name;
            $nickName = base64_encode(base64_decode($value->nickName)) === $value->nickName? base64_decode($value->nickName) : $value->nickName;
            $fidoID = $value->fidoID;
            $d = '';
            if($name !== '') {
                $d = $name;
            }
            if($d === '') {
               $d = $nickName;
            } else {
               $d = $d.'('.$nickName.')';
            }
            $d = $d.' '.$fidoID;
            if(strpos(strtolower($d), strtolower($request->name)) !== false) {
                array_push($data['players'], ['id' => $value->id, 'value' => base64_encode($d)]);
            }
        }
        return compact('data');
    }

    public function getEquipmentData(Request $request) {
        $data = [];
        $data['errorCode'] = 'er0000';
        if($request->distributor_id === null || $request->distributor_id === '') {
            $store = DB::connection('mysql')->select('SELECT id, name, fidoStoreId FROM store');
        } else {
            $sql = 'SELECT id, name, fidoStoreId FROM store WHERE distributor_id=:distributor_id';
            $store = DB::connection('mysql')->select($sql, ['distributor_id' => $request->distributor_id]);
        }
        foreach ($store as $key => $value) {
            $data['store'][$value->id] = [ 'fidoStoreId' => $value->fidoStoreId, 'name' => $value->name ];
        }
        $sql = 'SELECT id, name, distributor_id, store_id FROM iteam_pi';
        $data['equipments'] = DB::connection('mysql_video')->select($sql);
        return compact('data');
    }

}