<?php
	if (isset($_POST) && count($_POST)!=0) {
		$first_call = false;
	
		$errors = array();

		// Check user name. Must be more tnan 3 symbols
		if (isset($_POST['username'])) {
			$username = trim($core->sanitize($_POST['username']));
			if (strlen($username)==0) $errors[] = 'Username is not set';
		} else {
			$errors[] = 'Username is not set';
		}
		
		// Check passwords
		if (isset($_POST['password']) ) {
			$pass = trim($core->sanitize($_POST['password']));
			if ($pass=='') $errors[] = 'Password is empty';
		} else {
			$errors[] = 'Password1 or Password1 is not set';
		}
		
		// Ok try to auth
		if (isset($errors) && count($errors)==0) {
			if ($core->userAuth($username, $pass)) {
				
			} else {
				$errors[] = 'Wrong username or password';
			}
		}
		
	}
		
?>

<?php include_once($app['base'] . 'pages/header.php');  ?>
<link href="css/login.css" rel="stylesheet">
<section>
	<div class="signin"> 
		<div class="content"> 
			<h2><?php echo __('Sign in'); ?></h2>
			<form id="loginForm" style="width: 100%;" action="login" method="post">
				<div class="form">
					<div class="inputBox"><input name="username" type="text" required> <i><?php echo __('Username'); ?></i></div> 
					<div class="inputBox"><input name="password" type="password" required> <i><?php echo __('Password'); ?></i></div> 
					<!-- <div class="links"> <a href="#">Forgot Password</a> <a href="#">Signup</a></div>  -->
					<div class="inputBox"><input id = "butt_login" type="submit" value="<?php echo __('Login'); ?>"></div> 
				</div> 
			</form>
		</div> 
	</div> 
	<?php 
	 if (isset($first_call) && !$first_call) {
		 if (isset($errors) && count($errors)!=0) {
			echo '<div class="ffff">';
			foreach ($errors as $one) {
				echo '<span style="color: red; position: relative; width: 100%;">'. $one . '</span><br>';
			}
			echo '</div>';
		 }
	 }
	?> 	
</section>

<?php include_once($app['base'] . 'pages/footer.php');  ?>