			<!-- FOOTER -->
			<div class="row f80">
			  <div class="col-lg-12 col-sm-12 col-md-12 col-xl-12 evkFooter footer-dark footer-shadow-dark">
				<p style="text-align: center;">&copy; 2024 <?php echo $app['site_name']; ?>, Inc. &middot; <a href="cookie"><?php echo __("Privacy"); ?></a></p>
			  </div>
			</div>
			<!-- /FOOTER -->

		</div></div>
	</div> <!-- //content -->
	<div id="formcont"></div>
	<div id="gotop" class="container-top"><a href="#" class="top"></a></div>
	<?php if (!$core->isCookies()) { include_once($app['base'] . 'pages/cookies.php');  } else { echo '<script>function check_cookie_accepted(){};</script>'; } ?>
	<script src="css/bootstrap532/js/bootstrap.bundle.min.js"></script>
	<script src="js/evk.js<?php echo '?v=' . substr(md5(rand()),0,3); ?>"></script>
	<script src="js/sweetalert2@11.js"></script>
</body>
</html>