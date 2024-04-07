<?php
	/**
	 * @author  Yevhen K
	 * @date 01/04/2024
	 */

	/* GLOBAL SETTINGS */
	$app['author'] = 'Yevhen K';
	$app['DEBUG_MODE'] = false;
	$_DS = '/'; // DIRECTORY_SEPARATOR
	$app['site_base'] = $_DS;
	$app['base'] = $_SERVER['DOCUMENT_ROOT'].$app['site_base'];
	$app['cookies_pref'] = 'cct';
	$app['debug_index'] = 0;		// Correction from root folder
	$app['site_name'] = 'RE-MOVI';
	$app['domen'] = 'localhost';
	$app['book_form'] = false;
	
	/* LANGUAGES: to add another: 
		1) add code as a key, title and icon
		2) Put a file into /lang folder with translation
	*/
	$app['languages'] =array(
			'ru' => ['title' => 'Русский' , 'icon' => 'flag-russia'],
			'en' => ['title' => 'English' , 'icon' => 'flag-united-kingdom'],
			'lt' => ['title' => 'Lithuanian' , 'icon' => 'flag-lithuania'],
			'pt' => ['title' => 'Português' , 'icon' => 'flag-portugal'],
			'sk' => ['title' => 'Slovak' , 'icon' => 'flag-slovakia']
	);
	
	/* MENU SETTINGS, FORMAT:
		'style' => [
			'devider' => 'devider html code',
			'pre' => 'before item html code',
			'class'=>'item class name',
			'post' =>'after item html code',
			'sub_class' =>'class subitem',
			'sub_style' =>'style subitem'
		],	
		'route' => [ 'name' => 'Menu item name',
					 'access' => 'admin', 
					 'icon' => 'icon of iem', 
					 'link' => 'External link if needed',
					 'items' => [ ... Subitems ... ],
					]
	*/
	$app['top_menu'] = [
		'style' => [
			'devider' => '<li class="dropdown-divider"></li>',
			'pre' => '<li>',
			'class'=>'dropdown-item',
			'post' =>'</li>',
			'sub_class' =>'dropdown-submenu',
			'sub_style' =>'list-style: none;'
		],
		'menu'=> [
			'booking_history'	=> [
				'name' => 'View Booked Movies',
				'access' => 'admin',
				'icon' => 'fas fa-layer-group fa-fw',
					'items' => [
							'test1' => [ 'name' => 'Test1', 'access' => 'admin2', 'icon' => 'fas fa-layer-group fa-fw'],
							'test2' => [ 'name' => 'Test2', 'access' => 'admin2', 'icon' => 'fas fa-layer-group fa-fw',
								'items' => [
										'test3' => [ 'name' => 'Test3', 'access' => 'admin2', 'icon' => 'fas fa-layer-group fa-fw' ],
										'test4' => [ 'name' => 'Test4', 'access' => 'admin2', 'icon' => 'fas fa-layer-group fa-fw' ],
										],
							],
					],

			],
			'movie_form'	=> [ 'name' => 'Add movie', 'access' => 'admin', 'icon' => 'fas fa-edit fa-fw' ],
			'divider1' => [ 'access' => 'admin', 'type' => 'divider' ],
			'profile'	=> [ 'name' => 'My profile', 'access' => '', 'icon' => 'far fa-address-card fa-fw' ],
			'booking'	=> [ 'name' => 'My booking', 'access' => '', 'icon' => 'fas fa-layer-group fa-fw' ],
			'divider2' => [ 'access' => '', 'type' => 'divider' ],
			'logout'	=> [ 'name' => 'Exit', 'access' => '', 'icon' => 'fas fa-door-open' ],
		]
	];
	

	/* DB SETTINGS */
	$app['conn']=null;
	$app['table_prefix']='ca2_';
	$app['db_database']='webdevca2';
	$app['db_host'] = 'localhost';
	$app['db_user'] = 'cct';
	$app['db_pass'] = '--- password ---';
	
	/* EMAIL SETTINGS */
	$app['email']  = array (
		'SMTP' => '- smtp mail server -',
		'user' => '--- sender email ---',
		'pass' => '----- password -----',
		'port' => 587,
		'from' => '--- sender email ---'
	);
	
?>