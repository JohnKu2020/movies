<script>
	window.addEventListener("DOMContentLoaded", function(){
		init_carousel();
		load_movies();	   
	});

	<?php 

	if ($app['page']=='home' || $app['page'] == 'main') { ?>
	//Event listener to detect user clear the serch input
	document.getElementById('searchfilm').addEventListener('input', function(e) { 
		let search = document.getElementById('searchfilm').value.trim();
		if (search.length == 0) load_movies()         
	});

	function admin_tools(ev){
		var event = window.event; event.preventDefault();
		url=ev.target.getAttribute("data-url");
		document.location = 'movie_form/' + url;
		console.log(url);
	}	

	<?php } ?>
	
	function load_movies(){
		var search = document.getElementById('searchfilm').value.toLowerCase().trim();
		$.ajax({ url: ajax, type: "POST", dataType: 'json', cache: false, data: {'act':'list', 'searchtxt':search }, success: function(qdata){
				if (qdata && qdata.ok) {
					document.getElementById("main_content").innerHTML = qdata.content;
										
					// Add event listeners for languages
					var elements = document.getElementsByClassName("admin_btn"); 
					for (var i = 0; i < elements.length; i++) { elements[i].addEventListener('click', admin_tools, false); } 
					
				} else {
					Swal.fire({ title: "Error!", text: qdata.err_txt, icon: "error"});
				}
			}
		}).then( function() { });		
	}
	    
    // --------------------- carousel FUNCTIONS -----------------------------
    function init_carousel(){ // Init carousel programmatically

        // Create an object of bottstarp carousel
        const myCarouselElement = document.querySelector('#evk_carousel');
		if (myCarouselElement) {
			var carousel = new bootstrap.Carousel(myCarouselElement, { interval: 5000, touch: true, ride: true, wrap: true, keyboard: true });
			// Add event listeners for booking
			var elements = document.getElementsByClassName("book_carousel");
			if (elements) {
				for (var i = 0; i < elements.length; i++) { elements[i].addEventListener('click', carousel_make_book, false); }
			}			
		}
    }



    function carousel_make_book(){ // Event on carousel book button click
        var event = window.event; event.preventDefault();
		bookingForm();
    }
	
</script>

<div id="allover" class="row" style="margin-top: 56px;"><div class="col-lg-12 col-sm-12 col-md-12 col-xl-12">
	<div id="mainscreen">
		<!-- carousel -->
		<div class="row evkCarosel">
		  <div class="col-lg-12">
			<div id="evk_carousel" class="carousel slide" data-bs-ride="carousel">

				<div id="evk_carousel_cont" class="carousel-inner">
					<?php  echo $core->getCarouselContent();  // Show random carousel slides ?>
				</div>
				<button class="carousel-control-prev" type="button" data-bs-target="#evk_carousel" data-bs-slide="prev">
				  <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span>
				</button>
				<button class="carousel-control-next" type="button" data-bs-target="#evk_carousel" data-bs-slide="next">
				  <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span>
				</button>

			</div>
		  </div>
		</div>
		<!-- /carousel -->
		<div id="main_content"class="row row-cols-1 row-cols-md-3 g-4"></div>
		
<?php $app['book_form'] = true; ?>