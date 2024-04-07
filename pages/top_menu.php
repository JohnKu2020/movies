<header data-bs-theme="dark">
  <nav id="navbar" class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid">
      <a id="company" class="navbar-brand" href="home" title="Home"><img src="css/img/logo.png" style="width: 25px;"> RE-MOVI</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">

        <ul class="navbar-nav me-auto mb-2 mb-md-0">
		<?php
		
			if (!$core->isUserAuthorized()) {

				echo '<li class="nav-item"><a class="nav-link active" aria-current="page" href="login">'. __('Sign in'). '</a></li>
					  <li class="nav-item"><a class="nav-link" href="register">'. __('Register') . '</a></li>';
			}
		?>
        </ul>
		<?php if ($app['page']=='home' || $app['page'] == 'main') { ?>
		<div class="d-flex">
		  <div class="col-lg-12 col-sm-12 col-md-12 col-xl-12 search_block">
			<div class="search-box" style="display: block;">
				<div class="search-input" role="search" style="margin-right: 2px;">
					<!-- <form id="searchform" action="search/" role="search" method="get"> -->
						<input x-webkit-speech="" onwebkitspeechchange="this.form.submit()" type="search" class="form-control" placeholder="<?php echo __('Search by actor or film'); ?>" value="" name="q" id="searchfilm" autocomplete="off" onkeypress="searchkey(event);" aria-label="search">
					 <!-- </form> -->
				</div>
			</div>
			<button id="searchbutton" type="button" class="btn btn-secondary" onclick="searchbut(event);">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
					<path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"></path>
				</svg>
			</button>			
		  </div>
		</div>  
		<?php } ?>

<?php

		if (isset($app['DEBUG_MODE']) && $app['DEBUG_MODE']) {
			echo '<div class="nav pull-right ms-1"><button id="test_btn" type="button" class="btn btn-primary navbar-btn"><span class="glyphicon glyphicon-plus"></span> Test </button></div>';	
		}

		if ($core->isUserAuthorized()) {

			echo '<div class="dropdown ms-1">
				  <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"  type="button" id="dropdownMenuButton1"  aria-expanded="false">'. 
				  '<i class="fas fa-user-alt pe-2"></i>' .$core->userGetFullName() .
				  '</button>
				  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
				  
			echo $core->renderMenu($app['top_menu']);
			
			echo '</ul>
				</div>';
				
		}
?>

		<ul class="navbar-nav">
			<li class="nav-item dropdown">
				<?php echo $core->getLanguageMenu(); ?>
			</li>
		</ul>
    
	</div>	
    </div>
  </nav>
</header>