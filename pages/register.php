<?php

	$first_call = true;
	$OPT = false;
	if (isset($_POST) && count($_POST)!=0) {
		
		
		$is_recaptcha_ok  = false;
		if (isset($_POST['vercapval']) && $_POST['vercapval']) {
			$secret = '6Lfr26EpAAAAAKhcSGuTH2nJTWvUQhlvFSZOiMXq';
			$ip = $_SERVER['REMOTE_ADDR'];
			$response = $_POST['vercapval'];
			$rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$ip");
			$arr = json_decode($rsp, TRUE);
			if ($arr['success']) {
				$errors[] = $arr;
				$is_recaptcha_ok  = true;
			}
		}
		
		if (!$is_recaptcha_ok) $errors[] = __('recaptcha is not valid');
		
		
		$first_call = false;
		//$core->debug_array_print($_POST);$core->debug_array_print($arr);
		
		$errors = array();
		// Check user name. Must be more tnan 3 symbols
		if (isset($_POST['username'])) {
			$username = trim($core->sanitize($_POST['username']));
			if (strlen($username)==0 || strlen($username)<3) $errors[] = __('length must be more then') . ' 3';
		} else {
			$errors[] = __('error');
		}
		
		// Check passwords
		if (isset($_POST['password1']) && isset($_POST['password2']) ) {
			$pass1 = trim($core->sanitize($_POST['password1']));
			$pass2 = trim($core->sanitize($_POST['password2']));
			if ($pass1=='') $errors[] = 'Password is empty';
			if ($pass2=='') $errors[] = 'Confirm password';
			if ($pass1!=$pass2) $errors[] = __('Passwords must be the same');
			
		} else {
			$errors[] = __('error');
		}
		
		// Check emails
		if (isset($_POST['email']) ) {
			$email = trim($core->sanitize($_POST['email']));
			if ($email=='') {
				$errors[] = __('error');
			} else {
				if (!validateEmail($email)) $errors[] = __('Must be a valid email');
			}
		} else {
			$errors[] = __('error');
		}
		
		//Check if user with гуктфьу is already exists
		if (isset($errors) && count($errors)==0) {
			if ($core->isUserExist($username)) {
				$errors[] = __('User with this username already exists');
			} else {
				// Add user
				if ($core->addUser($username, $pass1, $email)) {

					header('Location: login');
					
				} else {
					$errors[] = __('error');
				}
			}
		}
		
	}
	function validateEmail($email) { return filter_var($email, FILTER_VALIDATE_EMAIL); }
	
	include_once('pages/header.php');
?>
<script src="https://www.google.com/recaptcha/api.js?render=6Lfr26EpAAAAAJl9--cb2gEn1I9U2g-Ed-lohIUq&hl=<?php echo $core->getCurrentLanguage(); ?>"></script>
<link href="css/login.css" rel="stylesheet">
<style>
section { height: 75vh!important; }
.boxerror { border: 2px red solid!important; }
.allerrors { margin: 10px 10px 10px 10px; z-index: 200000; position: relative; }
</style>

<script>
	window.addEventListener("DOMContentLoaded", function(){
		
		 document.getElementById('butt_register').addEventListener('click', function(e) {
			e.preventDefault();
			if (!validateRegisterForm()) return false;
			
 			grecaptcha.ready(function() {
			  grecaptcha.execute('6Lfr26EpAAAAAJl9--cb2gEn1I9U2g-Ed-lohIUq', {action: 'submit'}).then(function(token) {
				  document.getElementsByName('vercapval')[0].value = token;
				  document.getElementById("registerForm").submit();
			  });
			});
			
		 });
				
	});

    function validateRegisterForm(){ // Validate each input element in th form
        clearRegisterForm();
        var letters = /^[A-Za-z]+$/; // Pattern for only letters 
        var numbers = /\d/g;         // Pattern for only numbers		

		// Check username
		var minimum_letters = 3;
		var username_el = document.getElementsByName('username')[0], username = username_el.value.trim();
		if (isDef(username)) {
			if (username.length < minimum_letters) return showRegisterFormError(username_el, "<?php echo __('length must be more then'); ?> " + minimum_letters);
			if(!username.match(letters)) return showRegisterFormError(username_el, "<?php echo __('letters only'); ?>");
		} else {
			return showRegisterFormError(username_el, "<?php echo __('error'); ?>");
		}
		
		// Check password1
		var pass1_el = document.getElementsByName('password1')[0], password1 = pass1_el.value.trim();
		if (isDef(password1)) {
			if (password1.length < minimum_letters) return showRegisterFormError(pass1_el, "<?php echo __('length must be more then'); ?> " + minimum_letters);
		} else {
			return showRegisterFormError(pass1_el, "<?php echo __('error'); ?>");
		}

		// Check password2
		var pass2_el = document.getElementsByName('password2')[0], password2 = pass2_el.value.trim();
		if (isDef(password2)) {
			if (password2.length < minimum_letters) return showRegisterFormError(pass2_el, "<?php echo __('length must be more then'); ?> " + minimum_letters);
		} else {
			return showRegisterFormError(pass2_el, "<?php echo __('error'); ?>");
		}
		
		if (password1!=password2) return showRegisterFormError(pass2_el, "<?php echo __('Passwords must be the same'); ?>");

		// Check email
		var email_el = document.getElementsByName('email')[0], email = email_el.value.trim();
		if (isDef(email)) {
			if(!isEmail(email)) return showRegisterFormError(email_el, "<?php echo __('Must be a valid email'); ?>");
		} else {
			return showRegisterFormError(email_el, "<?php echo __('error'); ?>");
		}
		
		return true;
		
    }
    function showRegisterFormError(el, message){ // Show errors under input box
		el.setAttribute('class', 'boxerror');
        var spanTag = document.createElement("span");
        spanTag.setAttribute('class', 'validateerror');
        spanTag.innerHTML = message;
        el.parentNode.after(spanTag);
        return false;
    }	
	function clearRegisterForm(){
		var spans = document.querySelectorAll("span.validateerror");
		for (var i = 0, span; span = spans[i++];) { span.remove(); }
		var inputs = document.querySelectorAll("input.boxerror");
		for (var i = 0, input; input = inputs[i++];) { input.classList.remove("boxerror"); }
	}
</script>

<section>
	<div class="signin"> 
		<div class="content"> 
			<h2><?php echo __('Register'); ?></h2>
			<form id="registerForm" style="width: 100%;" action="register" method="post">
				<div class="form">
					<div class="inputBox"><input name="username" type="text" required> <i><?php echo __('Username'); ?></i></div>
					<div class="inputBox"><input name="password1" type="password" required> <i><?php echo __('Password'); ?></i></div> 
					<div class="inputBox"><input name="password2" type="password" required> <i><?php echo __('Confirm password'); ?></i></div> 
					<!-- <div class="links"> <a href="recover">Forgot Password</a> <a href="login">Signup</a></div>  -->
					<div class="inputBox"><input name="email" type="text" required> <i><?php echo __('Email'); ?></i></div>
					<div class="inputBox"><input id="butt_register" type="submit" value="<?php echo __('Sign up'); ?>"></div>
					<input name="vercapval" type="hidden">
				</div> 
			<form>
		</div> 
	</div> 
	<?php 
	 if (isset($first_call) && !$first_call) {
		 if (isset($errors) && count($errors)!=0) {
			echo '<div class="allerrors">';
			foreach ($errors as $one) {
				echo '<span style="color: red; position: relative; width: 100%;">'. $one . '</span><br>';
			}
			echo '</div>';
		 }
	 }
	?>   
</section>

<?php include_once($app['base'] . 'pages/footer.php');  ?>