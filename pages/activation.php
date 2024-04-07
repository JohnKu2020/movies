<?php
	include_once('pages/header.php');
	
	if ( isset($_GET) && count($_GET)!=0 && isset($_GET['code']) && isset($_GET['email']) ) {
	
		$code  = trim($core->sanitize($_GET['code']));
		$email  = trim($core->sanitize($_GET['email']));
		
		if ($code!='' && $email!='') {
			
			$ret = $core->userCheckOTP($email, $code);
			include_once($app['base'] . 'pages/page_header.php'); 
			echo '<div style="text-align:center"><h1>'. $ret['message'] .'</h1><div style="margin-bottom: 50px;"></div>';
			//$core->debug_array_print($ret);
			
		} else {
			page_not_found();
		}
	
	} else {
		page_not_found();
	}
	
	include_once($app['base'] . 'pages/footer.php'); 
?>