<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $core->_meta_data['title']; ?></title>
	<base href="<?php echo $app['site_base']; ?>">
    <meta name="description" content="<?php echo $core->_meta_data['description']; ?>">
	<meta name="keywords" content="<?php echo $core->_meta_data['keywords']; ?>">
	<meta name="robots" content="index,follow">
	<meta name="author" content="<?php echo $app['author']; ?>">
	<!-- Facebook -->
	<meta property="og:locale" content="en_EN" />
	<meta property="og:site_name" content="<?php echo $app['site_name']; ?>" />
	<meta property="og:updated_time" content="<?php echo date("r"); ?>" />
	<meta property="og:title" content="<?php echo $core->_meta_data['title']; ?>"/>
	<meta property="og:type" content="<?php echo $core->_meta_data['og_type']; ?>"> 
	<meta property="og:url" content="<?php echo $core->_meta_data['og_url']; ?>"/>
	<meta property="og:description" content="<?php echo $core->_meta_data['og_description']; ?>"/>
	<meta property="og:site_name" content="<?php echo $core->_meta_data['title']; ?>">
	<meta property="og:image" content="<?php echo $core->_meta_data['og_image']; ?>">
	<meta property="og:image:secure_url" content="<?php echo $core->_meta_data['og_image']; ?>" />
	<meta property="og:image:alt" content="<?php echo $core->_meta_data['title']; ?>" />
	<!-- Twitter -->
	<meta name="twitter:card" content="summary">
	<meta name="twitter:title" content="<?php echo $core->_meta_data['title']; ?>">
	<meta name="twitter:description" content="<?php echo $core->_meta_data['og_description']; ?>">
	<meta name="twitter:site" content="<?php echo $app['site_name']; ?>">
	<meta name="twitter:creator" content="<?php echo $app['author']; ?>">
	<meta name="twitter:image" content="<?php echo $core->_meta_data['og_image'];  ?>" />	
	
    <link rel="icon" type="image/x-icon" href="css/img/favicon.png">  
    <link href="css/bootstrap532/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/evk.css<?php echo '?v=' . substr(md5(rand()),0,3); ?>" rel="stylesheet">
	<link href="css/flags.css" rel="stylesheet">
	<script src="js/jquery-3.7.1.min.js"></script>
	<link href="css/animate.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	
</head>
<body class="body_e">
  <div class="content w800 cont_3d">
  <?php include_once($app['base'] . 'pages/top_menu.php'); ?>