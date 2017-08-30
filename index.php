<?php 
require __DIR__ . '/vendor/autoload.php';
require 'libs/notorm/NotORM.php';

use \Slim\App;
 
$app = new App();

$dbhost = 'localhost';
//id2726863_joni
$dbuser = 'root';
//kh0l154dm1n
$dbpass = '';
$dbname = 'mahasiswa_db';
$dbmethod = 'mysql:dbname=';

$dsn = $dbmethod.$dbname;
$pdo = new PDO($dsn, $dbuser, $dbpass);
$db = new NotORM($pdo);
 
$app-> get('/', function(){
    echo "Hello World. I'm Kholis";
});

$app->post('/postmhs',function($request, $response, $args)use($app, $db){
	$postmhs = $request->getParams();
	$result = $db->mahasiswa()->insert($postmhs);

	$responseJson["error"]=false;
	$responseJson["message"]="Data berhasil diinputkan";

	echo json_encode($responseJson);
});
	

$app->get('/mhs', function() use($app, $db){
	$mahasiswa["error"]=false;
	$mahasiswa["message"]="Berhasil mendapatkan data mahasiswa";
	foreach ($db->mahasiswa() as $data) {
		# code...
		$mahasiswa['semua_mahasiswa'][] = array(
			'npm'=>$data['npm'],
			'nama'=>$data['nama']);
	}
	echo json_encode($mahasiswa);
});

$app->get('/detilmhs/{npm}', function($request, $response, $args) use($app, $db){
	$mahasiswa = $db->mahasiswa()->where('npm', $args['npm']);
	$mahasiswadetil = $mahasiswa->fetch();

	if ($mahasiswa->count() == 0) {
		# code...
		$responseJson["error"] = true;
		$responseJson["message"] = "Nama mahasiswa belum tersedia di database";
        $responseJson["npm"] = null;
        $responseJson["nama"] = null;
        $responseJson["alamat"] = null;
        $responseJson["nohp"] = null;
	} else {
		$responseJson["error"] = false;
        $responseJson["message"] = "Berhasil mengambil data";
        $responseJson["npm"] = $mahasiswadetil['npm'];
        $responseJson["nama"] = $mahasiswadetil['nama'];
        $responseJson["alamat"] = $mahasiswadetil['alamat'];
        $responseJson["no_hp"] = $mahasiswadetil['no_hp'];
	}
	echo json_encode($responseJson);
});

$app->put('/updatemhs/{npm}', function($request, $response, $args) use($app, $db){
	$app->response()->header("Content-Type", "application/json");
	$updatemhs = $db->mahasiswa()->where('npm', $args['npm']);
	if ($updatemhs->fetch()) {
		# code...
		$post = $app->request()->put();
		$result = $updatemhs->update($post);
		echo json_encode(array(
			"error"=>false,
			"message"=>"Data mahasiswa telah diupdate"));
	}else{
		echo json_encode(array(
			"error"=>true,
			"message"=>"Data mahasiswa gagal diupdate"));
	}
});

$app->delete('/delmhs/{npm}', function($request, $response, $args) use($app, $db){
	$mahasiswa = $db->mahasiswa()->where('npm', $args['npm']);
	if ($mahasiswa->fetch()) {
		# code...
		$result = $mahasiswa->delete();
		echo json_encode(array(
			"error"=>false,
			"message"=>"Data mahasiswa telah dihapus"));
	}
	else{
		echo json_encode(array(
			"error"=>true,
			"message"=>"Gagal menghapus data mahasiswa"));
	}
});

$app->post('/matkul', function($request, $response, $args) use($app, $db){
	$matkul = $request->getParams();
	$result = $db->matkul()->insert($matkul);

	$responseJson["error"]=false;
	$responseJson["message"]="Data berhasil diinputkan";

	echo json_encode($responseJson);
});

$app->put('/updatemk/{id_matkul}', function($request, $response, $args) use($app, $db){
	$app->response()->header("Content-Type", "application/json");
	$updatemk = $db->matkul()->where('id_matkul', $args['id_matkul']);
	if ($updatemk->fetch()) {
		# code...
		$post = $app->request()->put();
		$result = $updatemk->update($post);
		echo json_encode(array(
			"error"=>false,
			"message"=>"Data mata kuliah telah diupdate"));
	}else{
		echo json_encode(array(
			"error"=>true,
			"message"=>"Data mata kuliah gagal diupdate"));
	}
});

$app->get('/getmatkul', function()use($app, $db){
	$matkul["error"]=false;
	$matkul["message"]="Berhasil mendapatkan data matkul";
	foreach ($db->matkul() as $data) {
		# code...
		$matkul['semua_matkul'][] = array(
			'id_matkul'=>$data['id_matkul'],
			'dsn_pengampu'=>$data['dsn_pengampu'],
			'nm_matkul'=>$data['nm_matkul']);
	}
	echo json_encode($matkul);
});

$app->delete('/delmatkul/{id_matkul}', function($request, $response, $args) use($app, $db){
    $matkul = $db->matkul()->where('id_matkul', $args);
    if($matkul->fetch()){
        $result = $matkul->delete();
        echo json_encode(array(
            "error" => false,
            "message" => "Matkul berhasil dihapus"));
    }
    else{
        echo json_encode(array(
            "error" => true,
            "message" => "Matkul ID tersebut tidak ada"));
    }
});
 
//run App()
$app->run();