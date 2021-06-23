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
        $data['equipments'] = [];
        if($request->distributor_id === null || $request->distributor_id === '') {
            $equipments = DB::connection('mysql_video')->select('SELECT id, name, store_id FROM iteam_pi');
        } else {
            $sql = 'SELECT id, name, store_id FROM iteam_pi WHERE distributor_id=:distributor_id';
            $equipments = DB::connection('mysql_video')->select($sql, ['distributor_id' => $request->distributor_id]);
        }
        if($request->distributor_id === null || $request->distributor_id === '') {
            $stores = DB::connection('mysql')->select('SELECT id, name, fidoStoreId FROM store');
        } else {
            $sql = 'SELECT id, name, fidoStoreId FROM store WHERE distributorId=:distributor_id';
            $stores = DB::connection('mysql')->select($sql, ['distributor_id' => $request->distributor_id]);
        }
        $s = [];
        foreach ($stores as $key => $value) {
            $s[$value->id] = [ 'fidoStoreId' => $value->fidoStoreId, 'name' => $value->name ];
        }
        foreach ($equipments as $key => $value) {
            $d = $value->name;
            if($value->store_id !== NULL) {
                $d = $d.' '.$s[$value->store_id]['name'].' '.$s[$value->store_id]['fidoStoreId'];
            }
            if(strpos(strtolower($d), strtolower($request->name)) !== false) {
                array_push($data['equipments'], ['id' => $value->id, 'value' => $d]);
            }
        }
        
        return compact('data');
    }

}