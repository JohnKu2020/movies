<?php
	/**
	 * @author  Yevhen K
	 * @date 01/04/2024
	 */
	 
	error_reporting(E_ALL);										// Just gonna see all errors and wornings
	@set_time_limit(0);											// For scripts that can consume more then default timeout
	@clearstatcache();											// Clear static cach to deliver a up-to-date content

	require_once realpath(dirname(__FILE__)) . '/config.php';	// Load config	
	define($app['cookies_pref']."_IN_dev_JK", "yes");			// To check is php script was launched from index.php

	$app['user_id']= 0;											// Init current user_id to 0 assuming his is not authirized
	$app['abspath']= __DIR__ . '/';								// Save ABS path
	$app['page'] = '';											// Current page
	$app['protocol'] = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';	// Which proto: http or https ?

	require_once($app['base'].'pages/functions_base.php');		// Include supplementary functions
	require_once($app['base'].'classes/core.class.php');		// Include the core of our app
	$core = new Core();											// Init the core
	$core->DatabaseConnect();
	$core->SessionStart();										// Launch sessions to make use distinguish possible
	$app['auth'] = $core->IsAuth();								// Check if user is already authirize
	$core->prepareSeoMeta();
	
	//$core->debug_array_print($app);
	// Set global error handle to keep trace of errors
	function global_error_handle_func($code, $msg, $file, $line) { global $core; $core->log_and_die($msg,$code, $file, $line, false); } set_error_handler("global_error_handle_func");

	$lang_file = $core->DetectLanguage(); //$app['lang'] = 'en'; //$app['lang'] = 'ru'; //FOR DEBUG ONLY
	include_once($lang_file);

	$core->initRoutes();
	if (isset($app['route']) && count($app['route'])!=0) {

			switch ($app['page']) {
				case 'movie':
					if (isset($app['route'][$app['debug_index']+1])) {
						include_once($app['base'] . 'pages/movies_one.php');
						exit;
					}
					break;
					
				case 'main': 
				case 'home': 
				case '': 
					page_by_def(); break;
			}

			// ============ ROUTES ====================
			$routes_dyn = array (
				// route, page to include, wrap to header and footer
				array('search','pages/search.php',false),
				array('register','pages/register.php',false),
				array('login','pages/login.php',false),
				array('logout','pages/logout.php',false),
				array('activate','pages/activation.php',false),
				array('profile','pages/profile.php',true),
				array('booking','pages/booking.php',true),
				array('booking_history','pages/booking_history.php',true),
				array('cookie','pages/cookie_policy.php', true),
				array('movie_form','pages/movie_form.php', true),
				

			);
			for ($i = 0; $i < count($routes_dyn); $i++) { 
				if ($routes_dyn[$i][0]==$app['page']) {
					if(is_readable($routes_dyn[$i][1])) {
						if ($routes_dyn[$i][2]) include_once('pages/header.php');
						include_once($app['base'] . $routes_dyn[$i][1]); 
						if ($routes_dyn[$i][2]) include_once('pages/footer.php'); 
						exit;
					} else {
						$core->log_and_die('File not found',3,$routes_dyn[$i][1],basename(__FILE__).' @ '.__FUNCTION__,false);
					}
				}
			}

			// ============ XMLHttpRequests ====================
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				//To hide real script path
				$ajax_routes = array (
					array('apiv2main','pages/ajax_data.php'),
				);
				for ($i = 0; $i < count($ajax_routes); $i++) { if ($ajax_routes[$i][0]==$app['route'][$app['debug_index']]) { include_once($app['base'] . $ajax_routes[$i][1]); exit; } }
			}

			// ============ 404 NOT FOUND ===========
			page_not_found();
		
	} else {
		page_by_def();
	}

	page_not_found();

	// ============================================ DEFAULT PAGES =======================================
	function page_not_found($page = ''){
		global $app, $core;
		$core->log_and_die('File not found',3,$app['route'],__FUNCTION__,false);
		$core->_meta_data['title'] = $app['site_name'] . ' | 404 Not Found';
		header('HTTP/1.0 404 Not Found', true, 404);
		if ($page=='') $page = 'pages/404.php';
		include($app['base'] . 'pages/header.php');
		include($app['base'] . $page);
		include($app['base'] . 'pages/footer.php');
		exit;
	}

	function page_by_def() {
		global $app, $core;
		$app['page']="main";
		include($app['base'] . 'pages/header.php');
		include($app['base'] . 'pages/movies_all.php');
		include($app['base'] . 'pages/footer.php');
		exit;
	}
?>