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
        	$sql = 'SELECT pi.id AS id, pi.no, pi.mac, pi.name, pi.password, pi.store_id, pi.distributor_id, cpi.status AS status, cpi.ip FROM iteam_pi AS pi LEFT JOIN iteam_connect_pi AS cpi ON cpi.pi_id=pi.id ORDER BY cpi.status DESC, pi.id ASC';
        	$data['data'] = DB::connection('mysql_video')->select($sql);

        	if($data['data']) {
        		$sql = 'SELECT id, fidoStoreId, name FROM store';
        		$store = DB::connection('mysql')->select($sql);
        	}
        } else {
        	$sql = 'SELECT pi.id AS id, pi.no, pi.mac, pi.name, pi.password, pi.store_id, pi.distributor_id, cpi.status AS status, cpi.ip FROM iteam_pi AS pi LEFT JOIN iteam_connect_pi AS cpi ON cpi.pi_id=pi.id WHERE pi.distributor_id=:distributor_id ORDER BY cpi.status DESC, pi.id ASC';
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
}