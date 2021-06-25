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

    public function addTournament(Request $request) {
        $data = [];
        $sql = 'INSERT INTO iteam_tournament (name) VALUES (:name)';
        $result = DB::connection('mysql_video')->insert($sql, ['name' => $request->data['name']]);
        if($result) {
            if(count($request->data['players']) > 0) {
                $id = DB::connection('mysql_video')->getPdo()->lastInsertId();
                $sql = 'INSERT INTO iteam_tournament_players (tournament_id, track, u_id, u_name, p_id, p_name) VALUES ';
                foreach ($request->data['players'] as $key => $value) {
                    $sql .= '('.$id.', '.($key+1).', \''.$value['u_id'].'\', \''.$value['u_name'].'\', '.$value['p_id'].', \''.$value['p_name'].'\')';
                    if($key < count($request->data['players'])-1) {
                        $sql .= ',';
                    }
                }
                $result = DB::connection('mysql_video')->insert($sql);
                if($result) {
                    $data['errorCode'] = 'er0000';
                } else {
                    $data['errorCode'] = 'er0001';
                }
            } else {
                $data['errorCode'] = 'er0000';
            }
        } else {
            $data['errorCode'] = 'er0001';
        }
        
        return compact('data');
    }

    public function getTournamentListData(Request $request) {
        $data = [];
        $data['errorCode'] = 'er0000';
        $sql = 'SELECT t.id, t.name, t.createTime, count(p.id) AS count FROM iteam_tournament AS t LEFT JOIN iteam_tournament_players AS p ON p.tournament_id=t.id WHERE is_delete=0 GROUP BY t.id';
        $data['data'] = DB::connection('mysql_video')->select($sql);
        return compact('data');
    }

    public function deleteTournament(Request $request) {
        $data = [];
        $sql = 'UPDATE iteam_tournament SET is_delete=1 WHERE id=:id';
        $result = DB::connection('mysql_video')->update($sql, ['id' => $request->id]);
        if($result) {
            $data['errorCode'] = 'er0000';
        } else {
            $data['errorCode'] = 'er0001';
        }
        return compact('data');
    }

    public function getTournamentData(Request $request) {
        $data = [];
        $data['errorCode'] = 'er0000';
        $data['data'] = [];
        $data['data']['name'] = '';
        $data['data']['players'] = [];
        $sql = 'SELECT t.name, p.u_id, p.u_name, p.p_id, p.p_name FROM iteam_tournament AS t LEFT JOIN iteam_tournament_players AS p ON p.tournament_id=t.id WHERE t.id=:id ORDER BY p.track ASC';
        $result = DB::connection('mysql_video')->select($sql, ['id' => $request->id]);
        foreach ($result as $key => $value) {
            $data['data']['name'] = $value->name;
            if($value->u_id) {
                $data['data']['players'][$key] = [
                    'u_id' => $value->u_id,
                    'u_name' => $value->u_name,
                    'p_id' => $value->p_id,
                    'p_name' => $value->p_name
                ];
            }
        }
        return compact('data');
    }

    public function updateTournament(Request $request) {
        $data = [];
        $sql = 'UPDATE iteam_tournament SET name=:name WHERE id=:id';
        $result = DB::connection('mysql_video')->update($sql, ['name' => $request->name, 'id' => $request->id]);
        if($result <= 1) {
            $players = DB::connection('mysql_video')->select('SELECT track FROM iteam_tournament_players WHERE tournament_id=:id ORDER BY track ASC', ['id' => $request->id]);
            for($i=0; $i<=7; $i++){
                if(isset($request->players[$i])) {
                    if(isset($players[$i])) {
                        $sql = 'UPDATE iteam_tournament_players SET u_id=\''.$request->players[$i]['u_id'].'\', u_name=\''.$request->players[$i]['u_name'].'\', p_id='.$request->players[$i]['p_id'].', p_name=\''.$request->players[$i]['p_name'].'\' WHERE track='.($i+1).' AND tournament_id='.$request->id;
                        DB::connection('mysql_video')->update($sql);
                    } else {
                        $sql = 'INSERT INTO iteam_tournament_players (tournament_id, track, u_id, u_name, p_id, p_name) VALUES (:id, :track, :u_id, :u_name, :p_id, :p_name)';
                        DB::connection('mysql_video')->insert($sql, [
                            'id' => $request->id,
                            'track' => $i+1,
                            'u_id' => $request->players[$i]['u_id'],
                            'u_name' => $request->players[$i]['u_name'],
                            'p_id' => $request->players[$i]['p_id'],
                            'p_name' => $request->players[$i]['p_name']
                        ]);
                    }
                } else {
                    if(isset($players[$i])) {
                        $sql = 'DELETE FROM iteam_tournament_players WHERE tournament_id=:id AND track=:track';
                        DB::connection('mysql_video')->delete($sql, [
                            'id' => $request->id,
                            'track' => $i+1
                        ]);
                    }
                }
            }
            $data['errorCode'] = 'er0000';
        } else {
            $data['errorCode'] = 'er0001';
        }
        return compact('data');
    }
}