<?php 
	include_once($app['base'] . 'pages/header.php');
	include_once($app['base'] . 'pages/page_header.php'); 
?>

<div id="search_content"class="row row-cols-1 row-cols-md-3 g-4">

<?php 
 
	$query_str = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
	parse_str($query_str, $query_params);
	if (isset($query_params) && $query_params!='') {
		//search here
		$core->debug_array_print($query_params);
	}

	//$core->debug_array_print($app);
	//exit; 
?>

</div>
	
<?php 
	include_once($app['base'] . 'pages/page_footer.php');
	include_once($app['base'] . 'pages/footer.php'); 
?>


