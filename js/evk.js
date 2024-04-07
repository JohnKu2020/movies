var films, film_idx=0, bookModal, filmscreen, mainscreen, homebtn; // cache objects
var search_flag = false; // Flag to detect a current mode: search or plain
var lang_id, cookie_lang_name = 'cct_lang'; 			//Language vars
var filmsToShow = []; // holds the list of films to show on the main screen
var ajax = 'apiv2main';

window.addEventListener("DOMContentLoaded", function(){
    DOMReady(); // Wait until all DOM structure is ready to explore and to be manipulated
});

    function DOMReady(){
		
		// SCROLL EVENTS
		document.addEventListener("scroll", (event) => {
		
			// Nav Bar
			const navigationBar = document.getElementById('navbar');
			if (window.scrollY > 200) { navigationBar.classList.add('sticky'); } else { navigationBar.classList.remove('sticky'); }
			
			// Go top 
			const gotop = document.getElementById('gotop');
			if (window.scrollY > 50) { gotop.style.display = 'block'; } else { gotop.style.display = 'none'; } 
			
			// Lazy images
			const images = document.querySelectorAll('img[data-src]');
			images.forEach(function(image) {
				if (image.getBoundingClientRect().top < window.innerHeight) {
					image.src = image.dataset.src;
					image.removeAttribute('data-src');
				}
			});
			
			// Load more movies
			if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
			 //1
			}
			
			
		});
				
		init();
		
    }


	function init() { // Main screen with carousel and films
		
		check_cookie_accepted();
		
		// Add event listeners for languages
		var elements = document.getElementsByClassName("langit"); 
		for (var i = 0; i < elements.length; i++) { elements[i].addEventListener('click', language_changed, false); } 
		
		var test_btn = document.getElementById("test_btn");
		if (test_btn) {
			test_btn.addEventListener('click', test_btn_func, false);
		}
		
		// Add event listeners for GoTop
		var gotop = document.getElementById("gotop");
		if (gotop) gotop.addEventListener('click', goToTOp, false);

	 }
	 
	function test_btn_func(e){ e.preventDefault();  test_ajax(); }	

	function test_ajax(){
        var xhttp = new XMLHttpRequest();
		xhttp.open("POST", ajax, true);
		xhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
		xhttp.responseType = 'json';
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
				console.log(this.response);
				var qdata = this.response;
				if (qdata) {
					if (qdata.ok) { console.log(qdata); } else { console.log('err:' + qdata.err_txt); }
				}
            }
        };
		xhttp.onerror = function(event) { console.log(e); };
		xhttp.onprogress = function(event) { console.log(`done: ${event.loaded} of ${event.total}`); }
		xhttp.send(JSON.stringify({ "act": "test" }));
	}

	
	
	function goToTOp(){
		var event = window.event; event.preventDefault();
		window.scrollTo({ top: 0, behavior: 'smooth' });
	}		
	
	function language_changed(ev){
		ev.preventDefault(); 											// Prevent default click event
		setCookie(cookie_lang_name,this.getAttribute("data-lang"),30);	// Save Cookie
		location.reload();												// Reload the page to apply selected language
	}

	function searchkey(e) {
		if (e.which  == 13) {
			var event = window.event; event.preventDefault();
			search_it(e.target);
		}
	}
	function search_it(e){
		console.log('Enter:' + e.value);
		load_movies();
		return false;
	}
	function searchbut(event) {
		load_movies();
	}
	
// --------------------- SUPLEMENTARY FUNCTIONS -----------------------------

	function isDef (v) { return v !== undefined && v !== null }
	function isEmail(email) { return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(email); }  

// --------------------- Cookie FUNCTIONS -----------------------------

	function setCookie(name,value,days) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "")  + expires + "; path=/";
	}
	function getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}
	function eraseCookie(name) {   
		document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
	}