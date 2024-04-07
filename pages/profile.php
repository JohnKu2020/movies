<?php 
	//Access is prohibited for unauthorized users and non-administrators
	if (!$core->isUserAuthorized() ) header('Location: main');

	if (isset($_POST) && isset($_POST['user_id'])) {
		$core->saveUser($_POST);
		header("Location: profile");
	}

	include_once($app['base'] . 'pages/page_header.php'); ?>
	<div style="text-align:center"><h1><?php echo __('Profile'); ?></h1><div style="margin-bottom: 50px;"></div>
	
<?php		
 		$profile =  $core->profileGetForm();
		if ($profile && $profile['content']) {
			
			echo $profile['content'];
			
		} else {
			echo '<span>'. __('Not found') . '</span>';
		}
?>
	
	</div>
	
<?php include_once($app['base'] . 'pages/footer.php');  ?>