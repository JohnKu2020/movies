<?php
	/**
	 * @author  Yevhen K
	 * @date 01/04/2024
	 */

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	use PHPMailer\PHPMailer\SMTP;
	
class Core {

	private $_requestMethod;
	private $lang_id = 'en';
	private $totalMoviesCount = 0;
	private $_user;
	public $_meta_data;
	public $auth = false;
		
	// ============================================ CONSTRUCTOR & DESTRUCTOR =========================

	public function __construct() {
		global $_id, $_arr_notifications;
		$act = __FUNCTION__." in ".__FILE__." at ".__LINE__;
		$this->_requestMethod = $_SERVER['REQUEST_METHOD'];
		
		self::InitVariables();
	}

	public	function __destruct() {
		global $app;
		unset($this->_requestMethod);
		unset($app);
	}
	
	private function InitVariables() {
		global $app;
		$act = __FUNCTION__." in ".__FILE__." at ".__LINE__;
		
		$app['admin'] = false;
		$app['auth']  = false;
	}

		
	// ====================================================== BOOKING ===================================================	

	public function userBbookings() {
		global $app;

		$query = "SELECT b.book_date, b.seats_count,  m.title, u.username
				  FROM " . $app['table_prefix']."bookings b " .
				 "JOIN " . $app['table_prefix']. "booking_seats bs ON bs.booking_id = b.id " .
				 "JOIN " . $app['table_prefix']. "movies m ON b.movie_id = m.id ".
				 "JOIN " . $app['table_prefix']. "users u ON b.user_id = u.id ".
				 "WHERE u.id = ". $app['user_id'] . 
				 " GROUP BY b.book_date, m.title, u.username, b.seats_count;";
		
		//die($query);
		$sql = self::doSQL($query, __FUNCTION__);
		if($sql && mysqli_num_rows($sql)>0) {
			$content = '<table class="table table-bordered border-primary table-hover">
			  <thead>
				<tr>
				  <th scope="col">#</th>
				  <th scope="col">'.__('Date').'</th>
				  <th scope="col">'.__('Movie').'</th>
				  <th scope="col">'.__('Seats').'</th>				  
				</tr>
			  </thead>
			  <tbody>';
			$cnt=1;  
			while($items = mysqli_fetch_assoc($sql)) {
				$content .= '<tr><td>' . $cnt . '</td><td>' . $items['book_date'] . '</td><td>' . $items['title'] . '</td><td>' . $items['seats_count'] . '</td></tr>';
				$cnt++;
			}
			$content .= '</tbody></table>';
			
			if ($cnt==1) $content = '<h3>' . __('No bookings found') . '</h3>';
		}		
		return array('content' => $content, 'count' => ($cnt-1));
	}

	public function bookingGetAll() {
		global $app;

		$query = "SELECT b.book_date, b.seats_count,  m.title, u.username, u.first_name, u.last_name, u.id  FROM " . $app['table_prefix']."bookings b " .
				 "JOIN " . $app['table_prefix']. "booking_seats bs ON bs.booking_id = b.id " .
				 "JOIN " . $app['table_prefix']. "movies m ON b.movie_id = m.id ".
				 "JOIN " . $app['table_prefix']. "users u ON b.user_id = u.id ".
				 "GROUP BY b.book_date, m.title, u.username, b.seats_count;";
		
		//die($query);
		$sql = self::doSQL($query, __FUNCTION__);
		if($sql && mysqli_num_rows($sql)>0) {
			$content = '<table class="table table-bordered border-primary table-hover">
			  <thead>
				<tr>
				  <th scope="col">#</th>
				  <th scope="col">'.__('Date').'</th>
				  <th scope="col">'.__('User').'</th>
				  <th scope="col">'.__('Movie').'</th>
				  <th scope="col">'.__('Seats').'</th>
				</tr>
			  </thead>
			  <tbody>';
			$cnt=1;  
			while($items = mysqli_fetch_assoc($sql)) {
				$user_name = $items['username'];
				if (isset($items['first_name']) && $items['first_name']!='') $user_name = $items['first_name'];
				if (isset($items['last_name']) && $items['last_name']!='') $user_name .= ' ' . $items['last_name'];
				
				$content .= '<tr><td>' . $cnt . '</td><td>' . $items['book_date'] . '</td><td>' . $user_name . '</td><td>' . $items['title'] . '</td><td>' . $items['seats_count'] . '</td></tr>';
				$cnt++;
			}
			$content .= '</tbody></table>';
			
			if ($cnt==1) $content = '<h3>' . __('No bookings found') . '</h3>';
		}		
		return array('content' => $content, 'count' => ($cnt-1));
	}		

	
	// ====================================================== SEO ===================================================

	public function prepareSeoMeta($film = null) {
		global $app;
		
		if (isset($film)) {

			$this->_meta_data = array(
				'title' =>  $film['title'] . ' | ' . $app['site_name'],
				'og_site_name' => $film['title'],
				'og_url' =>  $app['protocol'] . $app['domen'] .'/movie/'. $film['friendlyURL'],
				'og_image' => $app['protocol'] . $app['domen'] . '/upload/'.$film['pictures'],
				'og_type' => 'site',
				'og_description' =>self::ShortenString($film['description'],150),
				'description' => self::ShortenString($film['description'],150), 
				'keywords' => 'popular movies booking ' . $film['actors'] .' '. $film['directors']
			);
		
		} 
		
		// SEO Default
		if (!isset($this->_meta_data['title'])) $this->_meta_data['title'] = $app['site_name'];
		if (!isset($this->_meta_data['og_site_name'])) $this->_meta_data['og_site_name'] = $app['site_name'];
		if (!isset($this->_meta_data['og_url'])) $this->_meta_data['og_url'] = $app['protocol'] . $app['domen'];
		if (!isset($this->_meta_data['og_image'])) $this->_meta_data['og_image'] = $app['protocol'] . $app['domen'] . '/css/img/favicon.png';
		if (!isset($this->_meta_data['og_type'])) $this->_meta_data['og_type'] = 'site';
		if (!isset($this->_meta_data['og_description'])) $this->_meta_data['og_description'] = 'popular movies booking';
		if (!isset($this->_meta_data['description'])) $this->_meta_data['description'] = 'popular movies booking';
		if (!isset($this->_meta_data['keywords'])) $this->_meta_data['keywords'] = 'popular movies booking';
	
	}

	public function SeoMetaSetTitle($title) {
		global $app;
		$this->_meta_data['title'] = $title . '|' . $app['site_name'];
		$this->_meta_data['og_site_name'] = $title;
	}

	// ============================================ Carousel =======================================

	public function getCarouselContent(){
		global $app;
		
		$content = '';
		$randomFilms = self::getRandomInts(0,self::getTotalMoviesCount(),3);
	
		if ($randomFilms) {
			$query="SELECT * FROM ".$app['table_prefix']."movies WHERE predelete = 0 and id IN (". implode(',', $randomFilms) . ");";
			$sql = self::doSQL($query, __FUNCTION__);
			$cnt = 1;
			while($item = mysqli_fetch_assoc($sql)) {
				if ($item) {
					$active = ''; if ($cnt==1) $active = ' active';
					$content .= '<div class="carousel-item '.$active.'">
									<a class="filmclick" href="movie/'.$item['friendlyURL'].'" data-id="'.$item['id'].'">
										<img class="bd-placeholder-img" width="100%" height="100%" src="upload/'.$item['pictures'].'" alt="'.$item['title'].'" focusable="false">
									</a>
									<div class="container">
										<div class="carousel-caption text-start">
											<div class="car_over">
												<h1>'.$item['title'].'</h1>
												<p>'.$item['slogan'].'</p>
												<p><a class="btn btn-lg btn-primary" href="movie/'.$item['friendlyURL'].'" data-id="'.$item['id'].'">'.__('More').'</a></p>
											</div>
										</div>
									</div>
								</div>';
					$cnt++;			
				}
			}
		}
		return $content;
	}

	// ============================================ Movies =======================================

	public function getMovies($ss = 0,$ff = 0, $sSearch = '', $intCount = 21, $sSort = ''){
		global $app;
		
		$content = '';
		$done = false;
		$cnt = 0;
		$filters=array();
		

		// Limit records
		$filter_limit = ' LIMIT ';
		if ($ff==0 || $ss==0) { 
			if ($ss==0) $ss = $intCount; 
			$filter_limit .= $ss;
				} else {
			$filter_limit .= $ff.", ".$ss;
		}
		
		// Search string
		if (isset($sSearch) && $sSearch!='') {
			$q = self::sanitize($sSearch);
			$q =trim(mb_strtolower($q,'UTF-8'));
			if (isset($q) && $q!='') $filters[] = " LOWER(CONCAT( title, actors, directors, genres )) LIKE '%".$q."%' ";
			$filter_order = ' ORDER BY `title` DESC ';
		} else {
			$filter_order = ' ORDER BY RAND() ';
		}
		
		$filters[] = " predelete = 0";
		
		$filter_str = implode(' AND ', $filters);
		
		$query = "SELECT * FROM " . $app['table_prefix']."movies WHERE " . $filter_str . $filter_order . $filter_limit;
		
		$sql = self::doSQL($query, __FUNCTION__);
		if($sql && mysqli_num_rows($sql)>0) {

			while($item = mysqli_fetch_assoc($sql)) {
				if ($item) {
					$content .= '<div class="col"><div class="card">';

					if (self::isAdmin()) {
						$content.='<ul class="edit-tools"><li><button class="admin_btn edit tips" type="button" data-tip="' . __('Edit') . '" data-url="'.$item['friendlyURL'].'">&nbsp;&nbsp;&nbsp;</button></li></ul>';
					}
										
					$content .= '<a class="btn btn-lg film_link covers" href="movie/'.$item['friendlyURL'].'" data-id="'.$item['id'].'">
						<img data-src="upload/'.$item['cover'].'" src="upload/'.$item['cover'].'" class="card-img-top" alt="'.$item['title'].'"></a>
						<div class="card-body">
							<p class="card-text"><b>'.$item['title'].', ' .$item['year'].'</b></p>
						</div>
					</div></div>';
					$cnt++;			
				}
			}			
			
		} else {
				$done = true;
		}
		
		if ($content=='') $content = '<div style="width: auto;"><h1>' . __('Nothing found') .'</h1><img src="css/img/958843-200.png" style="width: 100%;"></div>';
		
		return array('ok' => true, 'content' =>$content, 'done' => $done, 'ss' => $ss, 'ff' => $ff, 'count' => $cnt, 'token' => self::get_token(), 'db' => '' );
	}


	public function getOneMovie($sShortLink){
		global $app;
		
		$data = null;
		
		if (trim($sShortLink)=='') return $data;
		
		$query = "SELECT * FROM " . $app['table_prefix']."movies WHERE predelete = 0 AND friendlyURL = '". $sShortLink . "' LIMIT 1";
		$sql = self::doSQL($query, __FUNCTION__);
		if($sql && mysqli_num_rows($sql)>0) {
			$item = mysqli_fetch_assoc($sql);
			if ($item) $data = $item;
		}
		return $data;
	}

	public function getDefaultMovie(){
		global $app;
		
		$film = [
			'id' => 0,
			'title' => '',
			'slogan' => '',
			'description' => '',
			'trailers' => '',
			'cover' => '',
			'pictures' => '',
			'directors' => '',
			'actors' => '',
		];
		return $film;
	}

	public function getTotalMoviesCount(){
		global $app;
		$tot = 0;
		$query= "SELECT COUNT(*) as tot FROM ".$app['table_prefix']."movies WHERE predelete = 0";
		$sql = self::doSQL($query, __FUNCTION__);
		if ($sql && mysqli_num_rows($sql)!=0) {
			$films = mysqli_fetch_assoc($sql);
			if (isset($films) && isset($films['tot'])) $tot = intval($films['tot']); 
		}
		$this->totalMoviesCount = $tot;
		return $this->totalMoviesCount;
	}

	public function userGetFullName(){
		global $app;

		$user_name = $app['login'];

		$query= "SELECT first_name, last_name, username FROM ".$app['table_prefix']."users WHERE id = ". $app['user_id'];
		$sql = self::doSQL($query, __FUNCTION__);
		if ($sql && mysqli_num_rows($sql)!=0) $usr = mysqli_fetch_assoc($sql);
		
		if ($usr) {
			if (isset($usr['first_name']) && $usr['first_name']!='') $user_name = $usr['first_name'];
			if (isset($usr['last_name']) && $usr['last_name']!='') $user_name .= ' ' . $usr['last_name'];
		}
		
		return $user_name;
	}


	public function profileGetForm(){
		global $app;


		$query= "SELECT * FROM ".$app['table_prefix']."users WHERE id = ". $app['user_id'];
		$sql = self::doSQL($query, __FUNCTION__);
		if ($sql && mysqli_num_rows($sql)!=0) $usr = mysqli_fetch_assoc($sql);
		
		if (!$usr) {
			$content = 'User not found'; 
			return array('content' =>$content);	
		}
		
		$content .= '<div class="container mt-5">
			<form id="userForm" action="profile" method="post">

				<div class="row g-2 align-items-center mt-5">
				  <div class="col-lg-2"><label for="firstname" class="col-form-label">' . __("First name") . '</label></div>
				  <div class="col-lg-10"><input type="text" name="firstname" class="form-control" placeholder="' . __("First name") . '" value="' . $usr['first_name']. '"></div>
				</div>

				<div class="row g-2 align-items-center mt-1">
				  <div class="col-lg-2"><label for="lastname" class="col-form-label">' . __("Last name") . '</label></div>
				  <div class="col-lg-10"><input type="text" name="lastname" class="form-control" placeholder="' . __("Last name") . '" value="' . $usr['last_name'] . '"></div>
				</div>			
				
				<div class="row g-2 align-items-center mt-1">
				  <div class="col-lg-2"><label for="username" class="col-form-label">' . __("username") . '</label></div>
				  <div class="col-lg-10"><input type="text" class="form-control" placeholder="' . __("username") . '" value="'. $usr['username'] . '" readonly disabled></div>
				</div>
				
				
				<div class="row g-2 align-items-center mt-1">
				  <div class="col-lg-2"><label for="email" class="col-form-label">' . __("Email") . '</label></div>
				  <div class="col-lg-10"><input type="text" class="form-control" placeholder="' . __("email") . '" value="' . $usr['email'] . '" readonly disabled></div>
				</div>

				<div class="row g-2 align-items-center mt-1">
				  <div class="col-lg-2"><label for="actors" class="col-form-label">' . __("phone") . '</label></div>
				  <div class="col-lg-10"><input type="text" name="phone" class="form-control" placeholder="' . __("phone") . '" value="' . $usr['phone'] . '"></div>
				</div>			

				 <div class="col-lg-12 text-center mt-5 mb-5">
					<button type="submit" class="btn btn-success mt-3 me-3" onclick="userSave();">' . __("Save") . '</button>
				</div>
				
				<input type="hidden" name="user_id" value="' . $usr['id'] . '">
				
			</form>
		</div>		
		';

		return array('content' =>$content);	
	
	}
	
	
	public function saveUser($data){
		global $app;

		$act = __FUNCTION__." in ".__FILE__." at ".__LINE__;
		$ok= false;
		
		$params=array();
		if (isset($data['firstname'])) $params[]="first_name='".self::sanitize($data['firstname'])."'";
		if (isset($data['lastname'])) $params[]="last_name='".self::sanitize($data['lastname'])."'";
		if (isset($data['phone'])) $params[]="phone='".self::sanitize($data['phone'])."'";

		$id = intval(self::sanitize($data['user_id']));

		$query_parts = implode(', ', $params);
		$query="UPDATE ".$app['table_prefix']. "users SET ".$query_parts." WHERE id = ".$id;
		//die('<br><br>' . $sql_str);
		$sql = self::doSQL($query, $act);
		if ($sql) $ok= true;
		return array('ok' =>$ok , 'message' => '', 'route' => 'profile', 'db' => $query );
		
	}


	public function getMovieForm($film){
		global $app;
		
		if (!isset($film)) {
			$film = self::getDefaultMovie();
			$content = '<h1>' . __('Add a new movie') . '</h1>';
		} else {
			$content = '<h1>' . __('Editing movie') . ' : '.$film['title'].'</h1>';
		}
		
		$content .= '<div class="container mt-5">
			<form id="movieForm" action="movie_form" method="post">

				<div class="row g-2 align-items-center mt-5">
				  <div class="col-lg-2"><label for="title" class="col-form-label">' . __("Movie title") . '</label></div>
				  <div class="col-lg-10"><input type="text" name="title" id="title" class="form-control" placeholder="' . __("Movie title") . '" value="' . $film['title']. '"></div>
				</div>

				<div class="row g-2 align-items-center mt-1">
				  <div class="col-lg-2"><label for="slogan" class="col-form-label">' . __("Slogan") . '</label></div>
				  <div class="col-lg-10"><input type="text" name="slogan" id="slogan" class="form-control" placeholder="' . __("Slogan") . '" value="' . $film['slogan'] . '"></div>
				</div>			

				<div class="row g-2 align-items-center mt-1">
				  <div class="col-lg-2"><label for="description" class="col-form-label">' . __("Description") . '</label></div>
				  <div class="col-lg-10">
					<textarea class="form-control" id="description" name="description" rows="5" placeholder="Enter description" required>'. $film['description'] .'</textarea>
				  </div>
				</div>	
				
				<div class="row g-2 align-items-center mt-1">
				  <div class="col-lg-2"><label for="year" class="col-form-label">' . __("Year") . '</label></div>
				  <div class="col-lg-10"><input type="text" name="year" id="year" class="form-control" placeholder="' . __("Year") . '" value="'. $film['year'] . '"></div>
				</div>
				
				
				<div class="row g-2 align-items-center mt-1">
				  <div class="col-lg-2"><label for="directors" class="col-form-label">' . __("Directors") . '</label></div>
				  <div class="col-lg-10"><input type="text" name="directors" id="directors" class="form-control" placeholder="' . __("Directors") . '" value="' . $film['directors'] . '"></div>
				</div>

				<div class="row g-2 align-items-center mt-1">
				  <div class="col-lg-2"><label for="actors" class="col-form-label">' . __("Actors") . '</label></div>
				  <div class="col-lg-10"><input type="text" name="actors" id="actors" class="form-control" placeholder="' . __("Actors") . '" value="' . $film['actors'] . '"></div>
				</div>			
				

				<div class="row g-2 align-items-center mt-1">
					<div class="col-lg-2"><label for="cover" class="col-form-label">' . __("Сover") . '</label></div>
					<div class="col-lg-10">
						<textarea class="form-control" id="cover" name="cover" rows="3" placeholder="Enter covers URLs separated by commas" required>'. $film['cover'] . '</textarea>
					</div>
				</div>

				<div class="row g-2 align-items-center mt-1">
					<div class="col-lg-2"><label for="pictures" class="col-form-label">' . __("Pictures") . '</label></div>
					<div class="col-lg-10">
						<textarea class="form-control" id="pictures" name="pictures" rows="3" placeholder="Enter pictures URLs separated by commas" required>'. $film['pictures'] .'</textarea>
					</div>
				</div>


				<div class="row g-2 align-items-center mt-1">
					<div class="col-lg-2"><label for="trailers" class="col-form-label">' . __("Trailers") . '</label></div>
					<div class="col-lg-10">
						<textarea class="form-control" id="trailers" name="trailers" rows="3" placeholder="Enter trailer URLs separated by commas" required>' . $film['trailers'] . '</textarea>
					</div>
				</div>
				
				 <div class="col-lg-12 text-center mt-5 mb-5">
					<button type="submit" class="btn btn-success mt-3 me-3" onclick="movieSave();">' . __("Save") . '</button>
					<button type="submit" class="btn btn-danger mt-3" onclick="movieDelete();">' . __("Delete") . '</button>
				</div>
				
				<input type="hidden" name="id" value="' . $film['id'] . '">
				
			</form>
		</div>		
		';

		return array('content' =>$content);

	}

	public function getBookSeats($data){
		global $app;

		$seats = array();

		$query = "SELECT bs.seat_number FROM " . $app['table_prefix']."bookings b " .
				 "JOIN " . $app['table_prefix']. "booking_seats bs ON bs.booking_id = b.id " .
				 "WHERE b.movie_id = ". $data['movie_id'] . 
				 " AND bs.date = '".$data['date']."' AND bs.time ='".$data['time']."' " .
				 " GROUP BY bs.seat_number;";
		
		//return array('ok' => true , 'date' => $query ); exit;
		
		$sql = self::doSQL($query, __FUNCTION__);
		if ($sql && mysqli_num_rows($sql)!=0) {
			$seats = mysqli_fetch_all($sql);
		}
		return array('ok' => true , 'seats' => $seats );
	}



	
	public function saveBooking($data){
		global $app;

		$data['date'] = self::sanitize($data['date']);
		$data['time'] =self::sanitize($data['time']);
		$data['movie'] =self::sanitize($data['movie']);
		$seats_num = count($data['seats']);

		$date = $data['date'] . " " . $data['time'];

		$query="INSERT INTO " . $app['table_prefix']. "bookings (book_date, movie_id, user_id, seats_count) VALUES ('"
										. $date . "'," 
										. $data['movie'] . "," 
										. $app['user_id'] . "," 
										. $seats_num
										. ");";
		
		//return array('ok' => true , 'date' => $query ); exit;
		
		$booking_id = self::doSQL_ROLLBACK($query, __FUNCTION__);

		foreach ($data['seats'] as $item){
			$query="INSERT INTO " . $app['table_prefix']. "booking_seats ( booking_id, movie_id, date, time, seat_number, status) VALUES("
									. $booking_id . ","
									. $data['movie'] . ",'"
									. $data['date'] . "','"
									. $data['time'] . "','"
									. $item . "',"
									."1"
									.");";
			//return array('ok' => true , 'date' => $query ); exit;						
			$sql = self::doSQL($query, __FUNCTION__);						
		}
		return array('ok' => true );
		
	}	

	public function getBookForm($film){
		global $app;
	
		$availableSeats = [
			['A1', 'A2', 'A3', 'A4', 'A5'],
			['B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7'],
			['C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7'],
			['D1', 'D2', 'D3', 'D4', 'D5', 'D6', 'D7', 'D8', 'D9'],
			['E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8', 'E9'],
			['F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'F7', 'F8', 'F9'],
			['G1', 'G2', 'G3', 'G4', 'G5', 'G6', 'G7', 'G8', 'G9']			
		];
		
		$content = '<div class="modal fade" id="book_form" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h1 class="modal-title fs-5" id="ModalLabel">'. __("Booking") . '  ' . $film['title'] . '</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			  </div>
			  <div class="modal-body">';		

		$content .= '<div class="row">
						<div class="col-md-12">
							<div class="movie_screen">' . __("Screen") .'</div>
							<div class="seat-map">
							<div id="loadig" class="centered"><div id="overlay"></div><img style="max-width: 100px;" src="css/img/loading.gif"/></div>';

		foreach ($availableSeats as $key => $value) {	
			$content .= '<div class="row">';
			foreach ($value as $key2 => $value2) {	
				$content .= '<div class="seat">' . $value2 . '</div>';
			}
			$content .= '</div>';
		}
		$content .= '</div>		
				<div class="row" style="align-items: baseline;">
					<div class="seat_ex sold">&nbsp</div>sold
					<div class="seat_ex available">&nbsp</div>available
					<div class="seat_ex selected">&nbsp</div>selected
				</div>';

		$content .= '<hr><div class="text-center">
							<div id="calendar" style="padding: 0px 50px 0px 50px;"></div>';
		

		$content .= '<form id="bookform" aciton="process_from.php" name="bookform" method="post">
		
						<div class="text-center">
							<select id="time" name="time" class="form-select" style= "width: 60%;">
							  <!--<option selected value="0">Select movie show time</option> -->
							  <option selected value="16:00">4:00pm</option>
							  <option value="18:00">6:00pm</option>
							  <option value="20:00">8:00pm</option>
							  <option value="22:00">10:00pm</option>
							</select>
						</div>
					
						<input id="movie_id" name="id" type="hidden" value="' . $film['id'] . '">
					</form>

				</div>
				</div>
			</div>
			
			  </div>
			  <div class="modal-footer">
				<div class="col-md-12 text-center">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . __("Cancel") .'</button>
					<button type="button" class="btn btn-primary" id="btn_submit">' . __("Book") .'</button>
				</div>	
			  </div>
			</div>
		  </div>
		</div>';
		
		return array('content' =>$content);
	}


	public function getBookFormOld($film){
		global $app;
		
		$content = '<div class="modal fade" id="book_form" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h1 class="modal-title fs-5" id="ModalLabel">'. __("Booking") . '  ' . $film['title'] . '</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			  </div>
			  <div class="modal-body">
				<form id="bookform" aciton="process_from.php" name="bookform" method="post">
				  <div class="mb-3">
					<input type="text" class="form-control" name="fname" data-type="name" data-min="3" placeholder="' . __("First name") . '">
				  </div>
				  <div class="mb-3">
					<input type="text" class="form-control" name="lname" data-type="name" data-min="3" placeholder="' . __("Last name") . '">
				  </div>             
				  <div class="mb-3">
					<input type="text" class="form-control" name="email" data-type="email" data-min="6" placeholder="Email">
				  </div>            
				  <div class="mb-3">
					<input type="text" class="form-control" name="phone" data-type="phone" data-min="10" placeholder="'. __("Phone") . '">
				  </div>                            
				  <div class="mb-3">
					<label for="message-text" class="col-form-label">' . __("Message (optional)") . ': </label>
					<textarea class="form-control" id="message-text" data-type="text" name="message-text"></textarea>
				  </div>
				  <div class="form-check">
					<input class="form-check-input" type="checkbox" value="" id="GDPR">
					<label class="form-check-label" for="GDPR">' . __("I agree to the terms and conditions, GDPR") .'</label>
				  </div>
				  <input id="film_id" name="film_id" type="hidden">
				</form>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . __("Cancel") .'</button>
				<button type="button" class="btn btn-primary" id="btn_book">' . __("Book") .'</button>
			  </div>
			</div>
		  </div>
		</div>';
		
		return array('content' =>$content);
	}

	public function deleteMovie($id){
		global $app;
		$act = __FUNCTION__." in ".__FILE__." at ".__LINE__;

		//$query="DELETE FROM ".$app['table_prefix'] ."movies WHERE id = ".$id; //Delete permanetly
		$query="UPDATE ".$app['table_prefix']. "movies SET predelete = 1 WHERE id = ".$id;
		$sql = self::doSQL($query, $act);
		return array('ok' => true , 'route' => 'home' );
		
	}


	public function saveMovie($data){
		global $app;
		$act = __FUNCTION__." in ".__FILE__." at ".__LINE__;
		$ok = true;
		
		$id = 0; if (isset($data['id'])) $id = intval($data['id']);
		
		if ($id == 0 ) {	// Insert
			$return = self::dbMovieInsert($data);
		} else {			//Update
			$return = self::dbMovieUpdate($data);		
		}
		$ok= false;
		return $return;
		
	}
	
	private function dbMovieInsert($onefilm){
		global $app;
		$act = __FUNCTION__." in ".__FILE__." at ".__LINE__;
		$ok= false;

		$onefilm['title'] = self::sanitize($onefilm['title']);
		$onefilm['slogan'] =self::sanitize($onefilm['slogan']);
		$onefilm['description'] = self::sanitize($onefilm['description']);
		$onefilm['cover'] = self::sanitize($onefilm['cover']);
		$onefilm['pictures'] = self::sanitize($onefilm['pictures']);
		$onefilm['directors'] = self::sanitize($onefilm['directors']);
		if (isset($onefilm['year'])) { $onefilm['year'] = intval(self::sanitize($onefilm['year'])); } else { $onefilm['year'] = 1999;}
		if (isset($onefilm['price'])) { $onefilm['price'] = self::sanitize($onefilm['price']); } else { $onefilm['price'] = 100;}
		$onefilm['price'] = self::sanitize($onefilm['price']);
		$onefilm['trailers'] = self::sanitize($onefilm['trailers']);
		$onefilm['friendlyURL'] = GetInTranslit($onefilm['title']);
		$onefilm['actors'] = self::sanitize($onefilm['actors']);
	
		$query ="INSERT INTO ".$app['table_prefix']. "movies (`friendlyURL`,`title`,`slogan`, `description`,`cover`, `pictures`,`year`, `price`, `directors`, `actors`, `trailers`) VALUES ('"
													.$onefilm['friendlyURL']."','"
													.$onefilm['title']."','"
													.$onefilm['slogan']."','"
													.$onefilm['description']."','"
													.$onefilm['cover']."','"
													.$onefilm['pictures']."',"
													.$onefilm['year'].","
													.$onefilm['price'].",'"
													.$onefilm['directors']."','"
													.$onefilm['actors']."','"
													.$onefilm['trailers']."'"
													.");";
		//die(json_encode(array('ok' => true, 'sql' => $query )));											
		$sql = self::doSQL($query, $act, false);
		if ($sql) $ok= true;
		
		return array('ok' =>$ok , 'message' => '', 'route' => 'movie/'.$onefilm['friendlyURL'] );
		
	}
	
	private function dbMovieUpdate($data){
		global $app;
		$act = __FUNCTION__." in ".__FILE__." at ".__LINE__;
		$ok= false;
		
		$params=array();
		if (isset($data['title'])) {
			$params[]="title='".trim(self::sanitize($data['title']))."'";
			$data['friendlyURL'] = GetInTranslit(trim(self::sanitize($data['title'])));
			$params[]="friendlyURL='".$data['friendlyURL']."'";
		}
		if (isset($data['slogan'])) $params[]="slogan='".self::sanitize($data['slogan'])."'";
		if (isset($data['pictures'])) $params[]="pictures='".self::sanitize($data['pictures'])."'";
		if (isset($data['description'])) $params[]="description='".self::sanitize($data['description'])."'";
		if (isset($data['cover'])) $params[]="cover='".self::sanitize($data['cover'])."'";
		if (isset($data['banner'])) $params[]="banner='".self::sanitize($data['banner'])."'";
		if (isset($data['year']) && $data['year']!='') $params[]="year=".self::sanitize($data['year']);
		if (isset($data['price']) && $data['price']!='') $params[]="price=".self::sanitize($data['price']);
		if (isset($data['directors'])) $params[]="directors='".self::sanitize($data['directors'])."'";
		if (isset($data['actors'])) $params[]="actors='".self::sanitize($data['actors'])."'";
		if (isset($data['trailers'])) $params[]="trailers='".self::sanitize($data['trailers'])."'";


		$id = intval(self::sanitize($data['id']));

		$query_parts = implode(', ', $params);

		//$temp=''; foreach ($params as $oneparam ) { $temp.=$oneparam.','; } if ($temp!='') $temp=rtrim($temp,','); 
		$query="UPDATE ".$app['table_prefix']. "movies SET ".$query_parts." WHERE id = ".$id;
		//die('<br><br>' . $sql_str);
		$sql = self::doSQL($query, $act);
		if ($sql) $ok= true;
		return array('ok' =>$ok , 'message' => '', 'route' => 'movie/'.$data['friendlyURL'] );
		
	}
	
	// =========================================== AUTH ===============================================
	
	public function SessionStart(){
		global $app;
		if (session_status() != PHP_SESSION_NONE) return;
		if (session_status() === PHP_SESSION_ACTIVE ) return;
		if (isset($app) && isset($app['session_start']) && $app['session_start'] === true)  return;

		ini_set('session.hash_bits_per_character', 5);
		ini_set('session.serialize_handler', 'php_serialize');
		ini_set('session.use_only_cookies', 1);
		ini_set('session.cookie_httponly', 1);
		ini_set("session.cookie_lifetime",0);
		ini_set('session.gc_maxlifetime', 3600);
		ini_set( 'date.timezone', 'Europe/Moscow');
		session_set_cookie_params(3600);
		$sess_name = session_name();
		session_status() === PHP_SESSION_ACTIVE ?: session_start();
		$app['session_start'] = true;
		setcookie($sess_name, session_id(), time()+60*60*24, $app['site_base']);
	}
	
	public function SessionStop($clear_sessions = false){
		global $app;
		if ($clear_sessions) {
			unset($_SESSION[$app['table_prefix'].$app['cookies_pref'].'_login']);
			unset($_SESSION[$app['table_prefix'].$app['cookies_pref'].'_pass']);
			unset($_SESSION[$app['table_prefix'].$app['cookies_pref'].'_token']);
			session_destroy();
			$app['session_start'] = false;
		}
	}
		
	public function IsAuth() {
		global $app;
		
		$act = __FUNCTION__." in ".__FILE__." at ".__LINE__;
		
		// Clear all timeout users
		$query="DELETE FROM ".$app['table_prefix'] ."user_sessions WHERE `date` < (NOW() - INTERVAL 12 HOUR);"; $sql = self::doSQL($query, $act, false);
		
		if(isset($_SESSION[$app['table_prefix'].$app['cookies_pref'].'_login']) && isset($_SESSION[$app['table_prefix'].$app['cookies_pref'].'_pass']) ) {
			
			$slogin=$_SESSION[$app['table_prefix'].$app['cookies_pref'].'_login'];
			$spass=$_SESSION[$app['table_prefix'].$app['cookies_pref'].'_pass'];
			if (isset($_SESSION[$app['table_prefix'].$app['cookies_pref'].'_token'])) $token=$_SESSION[$app['table_prefix'].$app['cookies_pref'].'_token'];
			$sql = "SELECT id, username, pass, first_name, last_name, group_id FROM ".$app['table_prefix']."users WHERE blocked = 0 AND (username='".$slogin."' OR phone='".$slogin."' OR email='".$slogin."' ) AND pass='".$spass."'";
					//die($sql);
			$res=mysqli_query($app['conn'], $sql);
			if ($res && mysqli_num_rows($res)!=0) {
				$user = mysqli_fetch_array($res);
				if($user) {

					if (password_needs_rehash($user['pass'], PASSWORD_DEFAULT)) {
						$newHash = password_hash($user['pass'], PASSWORD_DEFAULT);
						@mysqli_query($app['conn'],"UPDATE ".$app['table_prefix']."users SET pass='".$newHash."' WHERE id=".$user['id']);
					}
					
					$this->_user = $user;
					$app['user_id']= $user['id'];
					$app['login']= $user['username'];
					$app['token'] = $token;
					$app['admin'] = $user['group_id']==99 ? true : false;
	
					// Check token! -  MANY COMPUTERS TO ONE USER
					$result = mysqli_query($app['conn'], "SELECT token FROM ".$app['table_prefix']."user_sessions WHERE token='".$token."'");
					if ($result) {
						if (mysqli_num_rows($result) == 0) return false;
					} else {
						return false;
					}
					return true;
				}
			}
		}
		return false;
	}
	
	
	public function userAuth($username, $user_pass) {
		global $app;
		
		$act = __FUNCTION__." in ".__FILE__." at ".__LINE__;
		
		// Clear old sessions ===
		$query="DELETE FROM ".$app['table_prefix']."user_sessions WHERE `date` < (NOW() - INTERVAL 12 HOUR);"; $sql = self::doSQL($query, __FUNCTION__, false);
		
		// Check user
		if (isset($username) && $username!='' && isset($user_pass) && $user_pass!='' ){
			
			$query= "SELECT * FROM ".$app['table_prefix']."users WHERE blocked = 0 AND ( username='".$username."' OR phone='".$username."' OR email='".$username."' )"; 
			$res = self::doSQL($query, __FUNCTION__, false);
			if ($res && mysqli_num_rows($res)!=0 ) {
				$user = mysqli_fetch_assoc($res);
				if($user) {
					//die(password_verify($user_pass, $user['pass']));
					if (password_verify($user_pass, $user['pass'])) {
						if (password_needs_rehash($user['pass'], PASSWORD_DEFAULT)) {
							$newHash = password_hash($user_pass, PASSWORD_DEFAULT);
							mysqli_query($app['conn'],"UPDATE ".$app['table_prefix']."users SET pass='".$newHash."' WHERE id=".$user['id']);
						}
					} else {
						session_destroy();
						header('Location: login');
						exit;
					}

					$app['user_id']=$user['id'];
					$app['admin'] = $user['group_id']==99 ? true : false;
					$token= self::get_token();
					$_SESSION[$app['table_prefix'].$app['cookies_pref'].'_token']=$token;
					$_SESSION[$app['table_prefix'].$app['cookies_pref'].'_pass']=$user['pass'];
					$_SESSION[$app['table_prefix'].$app['cookies_pref'].'_login']=$username;
					$session=session_id(); $time=round(microtime(true) * 1000);
					
					$user_GMT = 0; //if(isset($_POST['gmt'])) { $user_GMT = floatval($_POST['gmt']); } else { if(isset($user['user_GMT'])) $user_GMT = floatval($user['user_GMT']);}

					mysqli_query($app['conn'],"UPDATE ".$app['table_prefix']."users SET active_date=NOW() WHERE id=".$user['id']);
					//system_log('username', true, json_encode($user_info));

					$result_token = mysqli_query($app['conn'], "SELECT count(*) as allcount from ".$app['table_prefix']."user_sessions WHERE token='".$token."'");
					$row_token = mysqli_fetch_assoc($result_token);
					if($row_token && $row_token['allcount'] > 0){
						mysqli_query($app['conn'],"UPDATE ".$app['table_prefix']."user_sessions set token='".$token."' WHERE user_id=".$user['id']);
					}else{
						$user_info = '';
						mysqli_query($app['conn'],"INSERT INTO ".$app['table_prefix']."user_sessions (user_id, date, token, user_info) values(".$user['id'].", NOW(), '".$token."','".json_encode($user_info)."')");
					}

					//header ("Location: http://" . $_SERVER [ "HTTP_HOST" ]. $_SERVER [ "REQUEST_URI" ]);
					header('Location: main');
					//exit;
				}
			}
		}
	
	}
	

	
	// ====================================================== USER ===================================================
	// ====================================================== USER ===================================================
	// ====================================================== USER ===================================================

	public function isUserExist($username){
		global $app;

		$query = "SELECT id FROM " . $app['table_prefix']."users WHERE username = '". $username . "' LIMIT 1";
		$sql = self::doSQL($query, __FUNCTION__);
		if($sql && mysqli_num_rows($sql)>0) return true;
		return false;
	}

	public function addUser($username, $pass1, $email) {
		global $app;
		
		$newHash = password_hash($pass1, PASSWORD_DEFAULT);		
		$query = "INSERT INTO ".$app['table_prefix']."users (`username`, `pass`, `email`, `data`) VALUES ('".$username."','".$newHash."','".$email."','');";
		$new_user = self::doSQL_ROLLBACK($query, __FUNCTION__);
		
		self::userGenerateOTP($new_user, $email, $username);
		
		return $new_user;
	}
	
	public function userGenerateOTP($user_id, $email, $username) {
		global $app;
		$sql = null;
		if ($user_id) {
			$OTP_code = md5(rand());
			$onefilmid_till = time() + (24*60*60); // current time + 24 hours
			$query = "INSERT INTO ".$app['table_prefix']."OTPs (`user_id`, `code`, `email`, `valid_till`) VALUES (".$user_id.",'".$OTP_code."', '".$email."',".$onefilmid_till.");";
			$sql = self::doSQL($query, __FUNCTION__);
			
			$activation_link = $app['domen'] . $app['site_base'] .'activate/?code=' . $OTP_code . '&email=' .$email;
			
			$data =array(
				'to' => $email,
				'from' => $app['email']['from'],
				'username' => $username,
				'subject' => $app['site_name'] . ': confirmation mail',
				'body' => 'Hi, '.$username.'!<br>To complete registration you need <a href="'.$activation_link.'" target="_blank">to activate your account</a>'
											.'<br>The link will be valid for 24 hours',
				'altbody' => 'This is the body in plain text for non-HTML mail clients'
			);
			
			self::sendMail_PHPMailer($data);
		}
		return $sql;
	}
	
	public function userCheckOTP($email, $code) {
		global $app;
		
		$ok = false;
		$message = '';
		$otp = null;
		$query = "SELECT * FROM " . $app['table_prefix']."OTPs WHERE  code = '". $code . "' AND email = '" . $email . "' LIMIT 1";
		$sql = self::doSQL($query, __FUNCTION__);
		if($sql && mysqli_num_rows($sql)>0) {
			$otp = mysqli_fetch_assoc($sql);
			if($otp) {

				//$otp['valid_till'] = time() + (24*60*60); // current time + 24 hours
				//$message = "Expire date: " . date("Y-m-d H:i:s", $otp['valid_till']);
				
				if ( $otp['valid_till'] <= time()) {
					$message =  __('The activation link is outdated');
				} else {
					if (intval($otp['activated']) != 0) $message = __('The activation link has already been used');
				}

				if ($message =='') {
					$message = __('Successfully activated') . '!';
					$ok = true;
					$query="UPDATE ". $app['table_prefix'] . "OTPs SET activated = 1 WHERE code = '". $code . "' AND email = '" . $email . "'";
					$sql = self::doSQL($query, __FUNCTION__);
				}
			}
		}
		return array('ok' => $ok, 'message' => $message);
		
	}	

	public function isUserHasActiveOTP($user_id) {
		global $app;

		$code = null; $has = false;
		$query = "SELECT code, valid_till FROM " . $app['table_prefix']."OTPs WHERE activated = 0 AND user_id = ". $user_id . " LIMIT 1";
		$sql = self::doSQL($query, __FUNCTION__);
		if($sql && mysqli_num_rows($sql)>0) {
			$code = mysqli_fetch_assoc($sql);
			if($code) $has = true;
		}
		return array('has' => $has, 'code' => $code);
	}
	
	// ====================================================== MAIL ===================================================	
	
	public function sendMail_PHPMailer($data) {
		global $app;
		
		$ret = false;

		require $app['base'] . 'classes/PHPMailer/src/Exception.php';
		require $app['base'] . 'classes/PHPMailer/src/PHPMailer.php';
		require $app['base'] . 'classes/PHPMailer/src/SMTP.php';
		
		$mail = new PHPMailer(true);
		
		try {
			//Server settings
			//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
			$mail->isSMTP();                                            //Send using SMTP
			$mail->CharSet   = "UTF-8";
			$mail->SMTPSecure   = 'tls'; 
			$mail->Host       = $app['email']['SMTP'];                  //Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
			$mail->Username   = $app['email']['user'];                  //SMTP username
			$mail->Password   = $app['email']['pass'];                  //SMTP password
			$mail->Port       = $app['email']['port'];                  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
			$mail->SMTPOptions = [
				'ssl' => [
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true,
				]
			];

			//Recipients
			$mail->setFrom($data['from'], $data['site_name']);
			$mail->addAddress($data['to'], $data['username']);
			$mail->addReplyTo($data['from'], $data['site_name']);
			//$mail->addCC('cc@example.com');
			//$mail->addBCC('bcc@example.com');
			 
			//Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = $data['subject'];
			$mail->Body    = $data['body'];
			$mail->AltBody = $data['altbody'];

			$ret = $mail->send();
			
		} catch (Exception $e) {
			//return $mail->ErrorInfo;
		}
		
		return $ret;

	}	
	
	
	public function isAdmin() {
		global $app;
		if (isset($app['admin']) && $app['admin']) return true;
		return false;
	}
	
	public function isUserAuthorized() {
		global $app;
		if (isset($app['auth']) && $app['auth']) return true;
		return false;
	}	
	


	// ====================================================== LOG ===================================================
	
	public function log_and_die($err_txt='', $err_num=0, $query='', $module='', $die=true, $extra_arr_data  = array(), $type = ''){
		global $app;
		$result = false;
		$type = 'system_error';
		if (!isset($app['user_id'])) $app['user_id'] = 0;
		$err_data = array( 'script' => $_SERVER['PHP_SELF'], 'module' => $module, 'err_num' => $err_num, 'err_message' => $err_txt, 'query' => $query, 'extra_data' => $extra_arr_data, 'type' => $type );
		if (is_array($err_data)) { $sdata = json_encode($err_data); } else { $sdata = $err_data; }
		$sql_str="INSERT INTO ".$app['table_prefix']."sys_logs (`user_id`,`ok`,`type`, `data`) VALUES (". $app['user_id'].", ".(integer)$result.", '".$type."', '".$sdata."');";
		try {
			$ret = mysqli_query($app['conn'],$sql_str);
			$db = ''; if ($this->isAdmin()) $db = $query;
			
		}	catch(Exception $e) {
			echo 'ERROR: ' .$e->getMessage();
		}
		if ($die) echo json_encode(array('ok' => false, 'err_txt' => $err_txt, 'err_num' => $err_num, 'db' => $db), JSON_UNESCAPED_UNICODE);
		if ($die) exit;
	}
	
	// ====================================================== DEBUG =================================================

	public function bp($arr){
		global $app, $opts;
		if (!isset($arr)) {
			die(json_encode(array('ok' => false, 'db' => $_POST)));
		} else {
			die(json_encode(array('ok' => false, 'db' => $arr)));
		}
	}

	public function bp_console($stuff, $_tag = true){ 
		if ($_tag) echo '<script type="text/javascript">';
		echo '$(document).ready(function () { console.log('.json_encode($stuff).'); });'; 
		if ($_tag) echo '</script>';
	}
	
	public function debug_array_print($arr){ print("<pre>".print_r($arr,true)."</pre>"); }
	

	// ====================================================== HELPERS ===================================================
	
	private function ShortenString($_str, $_symbols_only = 100) {
		global $app;
		$act = __FUNCTION__." in ".__FILE__." at ".__LINE__;
		$_outstr = '';
		
		if (isset($_str)) {
			$_outstr = mb_strimwidth($_str, 0, $_symbols_only, "...");
		}
		
		return $_outstr;
	}

	
	public function getRandomInts($min, $max, $count) {
		$numbers = range($min, $max);
		shuffle($numbers);
		return array_slice($numbers, 0, $count);
	}
	
	// ====================================================== SECURITY ===================================================
	
	public function only_AJAX(){
		global $_SERVER;
		if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') $this->log_and_die('Попытка прямого вызова!',0,'', __FUNCTION__." in ".__FILE__." at ".__LINE__);
	}

	public	function sanitize($string, $br = true, $strip = 0) {
		global $app;
		if(is_array($string) || is_object($string)) return;
		$string = trim($string);
		$string = mysqli_real_escape_string($app['conn'], $string);
		$string = htmlspecialchars($string, ENT_QUOTES);
		if ($br == true) {
			$string = str_replace('\r\n', ' <br>', $string);
			$string = str_replace('\n\r', ' <br>', $string);
			$string = str_replace('\r', ' <br>', $string);
			$string = str_replace('\n', ' <br>', $string);
		} else {
			$string = str_replace('\r\n', '', $string);
			$string = str_replace('\n\r', '', $string);
			$string = str_replace('\r', '', $string);
			$string = str_replace('\n', '', $string);
		}
		if ($strip == 1) {
			$string = stripslashes($string);
		}
		$string = str_replace('&amp;#', '&#', $string);
		return $string;
	}	
	
	public function get_token() {
		$token = openssl_random_pseudo_bytes(16); $token = bin2hex($token);
		return $token;
	}
	
	// ============================================= COOKIES  =======================================
	public function isCookies() {
		global $app;
		if (!isset($_COOKIE)) return false;
		if (!isset($_COOKIE[$app['cookies_pref'] . '_cookies' ])) return false;
		return true;
	}	
	
	// ============================================= ROUTES  ========================================
	public function initRoutes() {
		global $app;

		if ($_SERVER['REQUEST_URI'] != '/') {
			$url_path = self::sanitize(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
			$app['route'] = explode('/', trim($url_path, ' /'));
		}
		
		$app['page']=$app['route'][$app['debug_index']];		

	}		

	// ============================================ DATABASE ========================================

	public function DatabaseConnect() {
		global $app;

		if ($app) {
			if (!$app['conn']) {
				try {
					$app['conn'] = mysqli_connect($app['db_host'],$app['db_user'],$app['db_pass'],$app['db_database']);
					if (mysqli_connect_errno()) die('Unable to connect to database: ' . mysqli_connect_errno());
					if ($app['conn']) {
						
						mysqli_query($app['conn'], 'SET NAMES utf8mb4');
						mysqli_query($app['conn'], 'SET CHARACTER SET utf8mb4');
						
					} else {
						throw new Exception('Unable to connect');
					}
				} catch(Exception $e) {
					die('Unable to connect to database: ' . $e->getMessage());
					//throw new RuntimeException("Connect failed: %s\n", mysqli_connect_error());
				}
			}
		} else {
			die('Please, set up config file');
		}
		return $app['conn'];
	}

	public function doSQL($query, $act, $one_error_die = true) {
		global $app;
		$sql = mysqli_query($app['conn'],$query); if (!$sql) self::log_and_die(mysqli_error($app['conn']), mysqli_errno($app['conn']), $query, $act, $one_error_die);
		return $sql;
	}

	public function doSQL_ROLLBACK($query, $act, $one_error_die = true) {
		global $app;
		
		mysqli_query($app['conn'],'START TRANSACTION');
		$sql = mysqli_query($app['conn'], $query);
		$err_txt=mysqli_error($app['conn']);
		if($err_txt) { $err_num=mysqli_errno($app['conn']); mysqli_query($app['conn'],'ROLLBACK');  self::log_and_die($err_txt,$err_num, $query, $act, $one_error_die); }
		$last_id = mysqli_insert_id($app['conn']);
		mysqli_query($app['conn'],'COMMIT');		
		return $last_id;
	}	


	// ============================================ Language =======================================
	
	public function DetectLanguage(){
		global $app;
		// Detect from cookie
		if (isset($_COOKIE) && isset($_COOKIE[$app['cookies_pref']. '_lang'])) {
			$app['lang'] = $_COOKIE[$app['cookies_pref'].'_lang'];
		} else {
			if (!isset($app['lang'])) $app['lang'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); // Detect from browser
		}
		$tmp_file = $app['base'].'lang/lang_'.$app['lang'].'.php';
		if (!file_exists($tmp_file)) $tmp_file = $app['base'].'/lang/lang_en.php';
		return $tmp_file;
	}
	
 	public function InitCurrentLanguage(){
		global $app;
		if (isset($_COOKIE) && isset($_COOKIE[$app['cookies_pref'].'_lang'])) $this->lang_id = $_COOKIE[$app['cookies_pref'].'_lang'];
		if (!isset($this->lang_id) || $this->lang_id=='') $this->lang_id = 'en';
	} 
	public function getCurrentLanguage(){
		return $this->lang_id;
	}
	
	public function getLanguageMenu(){
		global $app;
		
		self::InitCurrentLanguage();
		$lang = self::getLanguageNamebyID();
		
		$cont = '<button id="btn_lang" data-lang="'.$this->lang_id.'" class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="'.$lang['icon'].' flag"></i>'.$lang['title'].'</button>
			<ul class="dropdown-menu dropdown-menu-dark">';
				foreach ($app['languages'] as $key => $onefilmue) {
					$active = ''; if ($key==$this->lang_id) $active = ' active';
					$cont .='<li><a class="dropdown-item langit'.$active.'" href="#" data-lang="'.$key.'"><i class="'.$onefilmue['icon'].' flag"></i>'.$onefilmue['title'].'</a></li>';
				}
		$cont .= '</ul>';
		return 	$cont;
	}

	private function getLanguageNamebyID(){
		global $app;
		if (isset($app['languages']) && is_array($app['languages']) && isset($app['languages'][$this->lang_id])) {
			return $app['languages'][$this->lang_id];
		}
	}	

	// ====================================================== MENU ===================================================

	public function getAccessDeniedHTML() {
		global $app, $_SERVER;
		$app_access_is_denied = '<div class="col-sm-12 text-muted text-center"><h1 style="margin-top: 25%;"><i class="icon-lock2" style="font-size:  44px;"></i>&nbsp;&nbsp;'. __('Access denied').'</h1></div>';
		return $_SERVER["REQUEST_URI"].' @ '.basename(__FILE__) . ' ' . __('Access denied') . ' ' . $app_access_is_denied;
	}

	public function pageUserCan() {
		global $app;

		foreach($app['access_map'] as $key => $irpage) {
			if ($irpage['page'] == $app['page']) {
				if (!$this->checkUserAccess($irpage['access'])) return false;
			}
		}
		return true;
	}

	
	public function checkUserAccess($right){
		global $app;
		if ($right == '') return true;
		if ($right == 'admin') { if (self::isAdmin()) { return true; } else { return false; } }
	}
	
	public function renderMenu($array){
		global $_menu, $app;
		$_mmenu ='';
		$app['access_map'] = [];
		$_menu = '';
		$_mmenu.= $this->_constuct_menu($array['menu'], $array['style']);
		return $_mmenu;
	}

	private function _constuct_menu($array, $arr_style){
		global $lvl, $_menu, $app;
		foreach($array as $key => $item) {
			if (isset($item['access'])) {	
				if (isset($item['type']) && $item['type'] == 'divider') {
					if (self::checkUserAccess($item['access'])) $_menu.= $arr_style['devider'];
				} else {
					$_link = $key; if (isset($item['link'])) $_link = $item['link'];
					$app['access_map'][] = [
						'page' => $_link,
						'access' =>$item['access'],
						'name' => $item['name']
					];
					if (self::checkUserAccess($item['access'])) {
						$_icon =''; if (isset($item['icon']) && $item['icon'] != '') $_icon = '<i class="'.$item['icon'].'"></i> ';
						$_active=''; if ($app['page'] == $_link) $_active=' active';
						$_menu.= $arr_style['pre'] . '<a class="'.$arr_style['class'] . $_active . '" href="'.$_link.'">'. $_icon . __($item['name']) . '</a>' . $arr_style['post'] ;
					}
				}

				if(isset($item['items'])) {
					$lvl++;
					$_sub_class=''; if (isset($arr_style['sub_class'])) $_sub_class= ' class="'.$arr_style['sub_class'] .'" ';
					$_sub_style=''; if (isset($arr_style['sub_style'])) $_sub_style= ' style="'.$arr_style['sub_style'] .'" ';
					$_menu.='<ul ' . $_sub_class . $_sub_style .'>';
					$this->_constuct_menu($item['items'],$arr_style);
					$_menu.='</ul><li>';
					$lvl--;
				} else {
					$_menu.='</li>';
				}
			}
		}
		return $_menu;
	}

}
?>