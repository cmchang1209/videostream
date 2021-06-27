<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiEquipmentController extends Controller
{
	public function getListData(Request $request)
    {
    	$data = [];
        $data['errorCode'] = 'er0000';
        if($request->distributor_id === null || $request->distributor_id === '') {
        	$sql = 'SELECT pi.id AS id, pi.no, pi.mac, pi.name, pi.password, pi.store_id, pi.distributor_id, cpi.status AS status, cpi.ip FROM iteam_pi AS pi LEFT JOIN iteam_connect_pi AS cpi ON cpi.pi_id=pi.id WHERE pi.is_delete=0 ORDER BY cpi.status DESC, pi.id ASC';
        	$data['data'] = DB::connection('mysql_video')->select($sql);

        	if($data['data']) {
        		$sql = 'SELECT id, fidoStoreId, name FROM store';
        		$store = DB::connection('mysql')->select($sql);
        	}
        } else {
        	$sql = 'SELECT pi.id AS id, pi.no, pi.mac, pi.name, pi.password, pi.store_id, pi.distributor_id, cpi.status AS status, cpi.ip FROM iteam_pi AS pi LEFT JOIN iteam_connect_pi AS cpi ON cpi.pi_id=pi.id WHERE pi.distributor_id=:distributor_id AND pi.is_delete=0 ORDER BY cpi.status DESC, pi.id ASC';
        	$data['data'] = DB::connection('mysql_video')->select($sql, ['distributor_id' => $request->distributor_id]);

        	if($data['data']) {
        		$sql = 'SELECT id, fidoStoreId, name FROM store WHERE distributorId=:distributor_id';
        		$store = DB::connection('mysql')->select($sql, ['distributor_id' => $request->distributor_id]);
        	}
        }

        if($data['data']) {
        	$sql = 'SELECT id, name FROM distributor';
        	$distributor = DB::connection('mysql')->select($sql);

        	foreach ($data['data'] as $key => $value) {
        		$d = collect($distributor)->where('id', $value->distributor_id)->first();
        		$value->d_name = $d->name;
        		if($value->store_id) {
        			$s = collect($store)->where('id', $value->store_id)->first();
        			$value->s_name = $s->name.'('.$s->fidoStoreId.')';
        		} else {
        			$value->s_name = '';
        		}
        	}
        }

        return compact('data');
    }

    public function getDistributorData(Request $request)
    {
    	$data = [];
        $data['errorCode'] = 'er0000';
        $data['data'] = [];
        $sql = 'SELECT id, name, nation, CONCAT( name, " ", nation ) AS fullname  FROM distributor';
        $distributor = DB::connection('mysql')->select($sql);
        if($distributor) {
        	foreach ($distributor as $key => $value) {
        		if($request->name) {
        			if(strpos(strtolower($value->name), strtolower($request->name)) !== false || strpos(strtolower($value->nation), strtolower($request->name)) !== false) {
        				array_push($data['data'], ['id' => $value->id, 'value' => $value->fullname]);
        			}
        		} else {
        			array_push($data['data'], ['id' => $value->id, 'value' => $value->fullname]);
        		}
        	}
        }

        return compact('data');
    }

    public function getStoreData(Request $request)
    {
    	$data = [];
        $data['errorCode'] = 'er0000';
        $data['data'] = [];
        $sql = 'SELECT id, fidoStoreId, name, CONCAT( name, " ", fidoStoreId ) AS fullname, distributorId  FROM store WHERE distributorId=:distributorId';
        $store = DB::connection('mysql')->select($sql, ['distributorId' => $request->distributorId]);
        if($store) {
        	foreach ($store as $key => $value) {
        		if($request->name) {
        			if(strpos(strtolower($value->name), strtolower($request->name)) !== false || strpos(strtolower($value->fidoStoreId), strtolower($request->name)) !== false) {
        				array_push($data['data'], ['id' => $value->id, 'value' => $value->fullname, 'distributorId' => $value->distributorId]);
        			}
        		} else {
        			array_push($data['data'], ['id' => $value->id, 'value' => $value->fullname, 'distributorId' => $value->distributorId]);
        		}
        	}
        }

        return compact('data');
    }

    public function add(Request $request)
    {
    	$data = [];
    	$no = 'v-'.date("yW");
    	$sql = 'SELECT no FROM iteam_pi WHERE no LIKE :no ORDER BY id DESC LIMIT 1';
    	$result = DB::connection('mysql_video')->select($sql, ['no' => $no.'%']);
    	if($result) {
    		$s = collect($result)->first();
    		$s = explode('-',$s->no);
    		$s = $s[2] * 1 + 1;
    		$no = $no.'-'.str_pad($s, 4, '0', STR_PAD_LEFT);
    	} else {
    		$no = $no.'-0001';
    	}
    	$sql = 'INSERT INTO iteam_pi (mac, no, name, distributor_id, store_id, description) VALUES (:mac, :no, :name, :distributo, :store, :description)';
    	$result = DB::connection('mysql_video')->insert($sql, [
    		'mac' => $request->mac,
    		'no' => $no,
    		'name' => $request->name,
    		'distributo' => $request->distributorId,
    		'store' => $request->storeId,
    		'description' => $request->description
    	]);
    	if($result) {
            $data['errorCode'] = 'er0000';
        } else {
            $data['errorCode'] = 'er0001';
        }
        return compact('data');
    }

    public function delete(Request $request)
    {
    	$data = [];
        $sql = 'UPDATE iteam_pi SET is_delete=1 WHERE id=:id';
        $result = DB::connection('mysql_video')->update($sql, ['id' => $request->id]);
        if($result) {
            $data['errorCode'] = 'er0000';
        } else {
            $data['errorCode'] = 'er0001';
        }
        return compact('data');
    }
}