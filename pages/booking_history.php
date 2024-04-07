<?php 
	//Protect page from unauthorized users
	if (!$core->isUserAuthorized() ) header('Location: main');
	if (!$core->pageUserCan()) {
		echo $core->getAccessDeniedHTML();
		include_once($app['base'] . 'pages/footer.php');
		exit;
	}

	include_once($app['base'] . 'pages/page_header.php'); ?>
	<div style="text-align:center">
	<h1><?php echo __('Current bookings'); ?></h1>
	
<?php		
		$booking =  $core->bookingGetAll();
		if ($booking && $booking['content']) {
			
			echo $booking['content'];
			
		} else {
			echo '<span>'. __('Not found') . '</span>';
		}
?>
	
	</div>
	
<?php include_once($app['base'] . 'pages/footer.php');  ?>