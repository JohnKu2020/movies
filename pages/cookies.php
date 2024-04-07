<style>
#evk-cookie-consent { height: 100vh; width: 100vw; position: fixed; top: 0; left: 0; resize: vertical; overflow: auto; z-index: 999999999; background: rgba(0, 0, 0, 0.7); display:  none; }
#evk-cookie-consent .cookie-consent-container { position: absolute; top: 50%; left: 20px; right: 20px; margin: -100px auto 0; background: #fff; padding: 20px; max-width: 500px; }
#evk-cookie-consent .cookie-consent-selection { text-align: right; }
#evk-cookie-consent button { border: none; padding: 10px 20px; margin: 10px 0 0 10px; background: none; font-size: 1.1em; }
#evk-cookie-consent button.cookie-consent-allow { background-color: #0d6efd; color: #fff; border-radius: 5px; }
#evk-cookie-consent button.cookie-consent-allow:focus, #evk-cookie-consent button.cookie-consent-allow:hover { background-color: #0dcaf0; cursor: pointer; }
#evk-cookie-consent button.cookie-consent-deny { padding: 5px 0; font-size: 0.9em; opacity: 0.8; }
#evk-cookie-consent button.cookie-consent-deny:focus, button.cookie-consent-deny:hover {  opacity: 1;  cursor: pointer; }
#evk-cookie-consent hr { margin: 15px 0; }
</style>
<div id="evk-cookie-consent">
  <div class="cookie-consent-container">
    <div class="cookie-consent-notice">
	<?php 
		echo '<h4>' . __('Guess what? Cookies!') .'</h4><hr><p>' .__('This website uses cookies to give our users the best experience') . '.<br>' .
		  __('You can find out more by reading our') .' <a href="cookie">cookie policy</a>.</p>';
	?>
    </div>
    <div class="cookie-consent-selection"><button id="cookie_ok" value="true" class="cookie-consent-allow" onclick="cookie_accepted();">Ok</button></div>
  </div>
</div>
<script>
	function cookie_accepted() { setCookie('<?php echo $app['cookies_pref']; ?>_cookies',true,30); document.getElementById('evk-cookie-consent').style.display = 'none'; }
	function check_cookie_accepted() { var cook = getCookie('<?php echo $app['cookies_pref']; ?>_cookies'); if (cook === undefined || cook === null ) document.getElementById("evk-cookie-consent").style.display = 'block';
	}
</script>