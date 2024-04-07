<?php
	if (isset($app['base'])) { require_once($app['base'].'config.php'); } else { require_once('../config.php'); }
	$act = __FUNCTION__." in ".__FILE__." at ".__LINE__;
	require_once($app['base'].'classes/core.class.php');
	$core = new Core();
	$core->DatabaseConnect();
	$core->SessionStart();
	$auth = $core->IsAuth();

	include_once($app['base'].'pages/functions_base.php');

	//die(json_encode(array('ok' => true, 'db' =>$_POST, 'db2' =>$_GET, 'db4' => $data )));

	If (!isset($_POST)) { // Request from fetch OR XMLHttpRequest
		$raw=file_get_contents("php://input");
		$data=json_decode(html_entity_decode($raw),true); if (json_last_error()!=JSON_ERROR_NONE) $core->log_and_die(json_last_error_msg(), json_last_error(), "", $act);
	} else { // Request from aJAX
		$data=$_POST;
	}
	//die(json_encode(array('ok' => true, 'db' =>$_POST, 'db2' =>$_GET, 'db4' => $data )));

	if (isset($data) && isset($data['act'])) $act=$core->sanitize( $data['act']);
	if (!isset($act)) $core->log_and_die('No act',0,'',$act);

	function ajax_import_handle_func($code, $msg, $file, $line) { global $core;  $core->log_and_die($msg,$code, $file, $line, false); }; set_error_handler('ajax_import_handle_func');

switch ($act) {

	case "deleteMovie":
		$id = intval($core->sanitize( $data['id']));
		if ($id==0) $core->log_and_die('No id',0,'',$act);
		
		$ret = $core->deleteMovie($id);
		
		echo json_encode($ret);
		exit;
		break;

	case "getSeats":
		$ret = $core->getBookSeats($data);
		echo json_encode($ret);
		exit;
		break;
		
	case "saveBooking":
		$ret = $core->saveBooking($data['data']);
		echo json_encode($ret);
		exit;
		break;		
	
		echo json_encode(array('ok' => $ret, 'err_txt' => '', 'db' => 0 , 'app'=> 1 ));
		exit;
		break;		

	case "list":
		if (isset($data['ff'])) { $ff=$core->sanitize($data['ff']); } else {$ff=0;}
		if (isset($data['ss'])) { $ss=$core->sanitize($data['ss']); } else {$ss=0;}
		if (isset($data['searchtxt'])) { $sSearch=$core->sanitize($data['searchtxt']); } else { $sSearch= '';}

		echo json_encode($core->getMovies($ss,$ff, $sSearch));
		exit;
		break;

	case "import_JSON_data":
		die(json_encode(array('ok' => true, 'items' => $data['data'])));

		$cnt = 0;
		// ============= Items ---------------------
		$films = array(); $onefilm = array(); 
		foreach ($data['data'] as $Ind => $Val) {
			if (isset($Ind) && $Ind!='') {

				$onefilm['title'] = $core->sanitize($Val['title']);
				$onefilm['slogan'] = $core->sanitize($Val['slogan']);
				$onefilm['description'] = $core->sanitize($Val['description']);
				$onefilm['cover'] = $core->sanitize($Val['cover']);
				$onefilm['banner'] = $core->sanitize($Val['banner']);
				$onefilm['director'] = $core->sanitize($Val['director']);
				$onefilm['year'] = $core->sanitize($Val['year']);
				$onefilm['price'] = $core->sanitize($Val['price']);
				$onefilm['trailer'] = $core->sanitize($Val['trailer']);
				$onefilm['friendlyURL'] = GetInTranslit($onefilm['title']);
				$onefilm['actors'] = $core->sanitize(implode(";", $Val['actors']));

				$films[] = $onefilm;
				
				$query ="INSERT INTO ".$app['table_prefix']. "movies (`friendlyURL`,`title`,`slogan`, `description`,`cover`, `pictures`,`year`, `price`, `directors`, `actors`, `trailers`) VALUES ('"
															.$onefilm['friendlyURL']."','"
															.$onefilm['title']."','"
															.$onefilm['slogan']."','"
															.$onefilm['description']."','"
															.$onefilm['cover']."','"
															.$onefilm['banner']."',"
															.$onefilm['year'].","
															.$onefilm['price'].",'"
															.$onefilm['director']."','"
															.$onefilm['actors']."','"
															.$onefilm['trailer']."'"
															.");";
				$sql = $core->doSQL($query, $act, false);
				$cnt++;
			}				
		}

		echo json_encode(array( 'ok' => true, 'records_done' => $cnt, 'items' => $films));
		exit;
		break;
		
	// ================================  DEFAULT ===============================
	default	:
		$core->log_and_die('Unknown method',0,'',$act);
		exit;
		break;		
		
}
?>