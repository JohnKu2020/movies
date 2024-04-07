	<?php
	
		$shortlink = $core->sanitize($app['route'][$app['debug_index']+1]);
		$film = $core->getOneMovie($shortlink);
		
		if (!isset($film)) page_not_found();

		$core->prepareSeoMeta($film);
		
		include_once($app['base'] . 'pages/header.php');
	
	?>

	<script type="text/javascript" src="js/evk_calendar_jk.js"></script>
	<link href="css/evk_calendar_jk.css" rel="stylesheet" type="text/css">	
	
<script>
	var selectedDate = new Date().toISOString().slice(0, 10);
	$(document).ready(function (e) {

 		var book_btn = document.getElementById('book_btn');
		if (book_btn) {
			book_btn.addEventListener('click', bookingForm, false);
		}

 		var btn_submit = document.getElementById('btn_submit');
		if (btn_submit) {
			btn_submit.addEventListener('click', saveBookingForm, false);
		}
		
		$('#calendar').evkJKcalendar({canPast: false, lang: 'en', initDate:selectedDate });
		$("#calendar").on('change',function(e, date){ selectedDate = date; updateMovieSeats(); });
		$("#time").on('change',function(e){ updateMovieSeats(); });
		$('.seat').on('click', function(el){ if (!$(this).hasClass('sold')) $(this).toggleClass('selected'); });
		
	});

	function admin_tools(el){
		var event = window.event; event.preventDefault();
		url=el.getAttribute("data-url");
		document.location = 'movie_form/' + url;
	}

	function loading(st){ if (st) { document.getElementById('loadig').style.display = 'block'; } else { document.getElementById('loadig').style.display = 'none'; } }

	function updateMovieSeats(){
		loading(true);
		var date, mdate = $('#calendar').find('td.selected').attr('data-id'), mtime = $('#time').val();
		if (mtime==0 || !isDef(mdate)) mdate = new Date().toISOString().slice(0, 10);
		
 		$.ajax({ url: ajax, type: "POST", dataType: 'json', cache: false, data: {'act': 'getSeats', 'date': mdate, 'time': mtime, 'movie_id':$('#movie_id').val() }, success: function(qdata){
			if (qdata && qdata.ok) {
				$('.seat').removeClass('selected').removeClass('sold');
				if (qdata.seats) {
					$.each(qdata.seats, function(index, item){
						$('div.seat:contains(' + item + ')').addClass('sold');
					});						
				}
			} else {
				// Error from server
			}
		}
		}).then( function() { loading(false); });
	}

	function saveBookingForm(){
		var event = window.event; event.preventDefault();

		if ($('.seat.selected').length==0) { Swal.fire({ title: "<?php echo __('No seats selected'); ?>"}); return false; }
		var mdate = $('#calendar').find('td.selected').attr('data-id');
		if (!isDef(mdate)) { Swal.fire({ title: "<?php echo __('No movie show DATE selected'); ?>"}); return false; }
		if ($('#time').val()==0) { Swal.fire({ title: "<?php echo __('No movie show TIME selected'); ?>"}); return false; }
		
		var seats = [];
		$('.seat.selected').each(function(i,elem) { seats.push($(this).text()); });
		
		var data = {
			'seats': seats,
			'date' : selectedDate,
			'time' : $('#time').val(),
			'movie' : $('#movie_id').val()
		};

 		$.ajax({ url: ajax, type: "POST", dataType: 'json', cache: false, data: {'act': 'saveBooking', 'data': data}, success: function(qdata){
			if (qdata && qdata.ok) { 
				Swal.fire({ title: "<?php echo __('Booked'); ?>!"});
				bookModal.hide();
			} else {
				Swal.fire({ title: "<?php echo __('Error while saving'); ?>!"});
			}
		}
		});		

	}
	

	function bookingForm(){
		var event = window.event; event.preventDefault();
		
		<?php if (!$core->isUserAuthorized()) {
			$footer  = '\'<a href="register">' . __('Register'). '</a> '.__('or'). ' <a href="login">' . __('Sign in'). '</a>\''; 
			echo 'Swal.fire({ title: "'. __('In order to book a movie you should be a registered user') . '",
			  footer: '.$footer.' });
			return false;';
			}
		 ?>

		var allover = document.getElementById("allover");
		if (allover) allover.setAttribute('class', 'mblur'); 
				
		bookModal = new bootstrap.Modal('#book_form', { keyboard: false }); // Create an object of modal window
        bookModal.show();   //Show the form for booking
		var myModalEl = document.getElementById('book_form')
		myModalEl.addEventListener('hidden.bs.modal', function (event) { allover.classList.remove('mblur'); });
		updateMovieSeats();
	}


</script>	
	<div id="allover" class="row" style="margin-top: 56px;">
		<div class="col-lg-12 col-sm-12 col-md-12 col-xl-12">

	<?php
		$actors_list = '';
		$actors = explode(";", $film['actors']);
		foreach ($actors as $one) { 
			$actors_list .= '<span class="badge text-bg-secondary mx-1">'.$one.'</span>';
		}
		
		//novideo.jpg
		echo '<div id="filmscreen">
			<div class="row">
			  <div class="col-lg-12 col-sm-12 col-md-12 col-xl-12">
				<div class="filminfo">
				  <img id="banner" class="bd-placeholder-img" width="100%" height="100%" src="upload/'.$film['pictures'].'" alt="'.$film['title'].'">
				  		  <h1 id="title">'.$film['title'].', '.$film['year'].'</h1>
						  <h5 id="slogan">'.$film['slogan'].'</h5>
						  <p id="finfo"><b>'.__('Director').':</b> '.$film['directors'].'</p>
						  <span id="actors" class="evkActors"><b>'.__('Actors').':</b> '.$actors_list.'</span>
						  <p id="description" class="evkDesript">'.$film['description'].'</p>
					  <div>
				</div>
			  </div>
			</div>
			<div class="row">
			  <div class="col-lg-12 col-sm-12 col-md-12 col-xl-12 mx-auto">
				 <iframe id="trailer" width="90%" height="315" src="'.$film['trailers'].'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			  </div>
			</div>
			<div class="row">
			  <div class="col-lg-6 text-center">
				<p style="margin-top: 50px;"><a id="book_btn" class="btn btn-lg btn-primary book_carousel" href="#" data-id="'.$film['id'].'">'.__('Book now').'!</a></p>            
			  </div>';
			
			if ($core->isAdmin()) {
				echo 	'<div class="col-lg-6">
				<p class="text-end"><a id="edit_btn" class="btn btn-lg btn-success" href="#" data-url="'.$film['friendlyURL'].'" onclick="admin_tools(this);">'.__('Edit').'</a></p>            
			  </div>';
			}
			 
		echo '</div>
		  </div>';

	?>
		
		</div>
	</div>
	
	<?php 
		
		include_once($app['base'] . 'pages/footer.php'); 
	
		//echo $core->getBookFormOld($film)['content'];
		echo $core->getBookForm($film)['content'];
		
	?>