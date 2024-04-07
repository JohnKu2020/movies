<style>
.ok{color:green;}
.err{color:red;}
</style>

<?php

	/**
	 * @date    15/03/24
	 * @author  Yevhen Kuropiatnyk
	 * @email   evgeniy.kuropyatnik@gmail.com
	 * @student sba23066
	 */

include_once('../config.php');
include_once($app['base'] . 'connect_db.php');

$install = true;
$fill_tables = true;

$queries = array();

// Create Database with name from config
$queries[]="CREATE DATABASE IF NOT EXISTS " . $app['db_database'];

$queries[]="CREATE TABLE IF NOT EXISTS ".$app['table_prefix']."sys_logs (
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`user_id` int(11) NOT NULL DEFAULT '0',
	`type` varchar(128) NOT NULL DEFAULT '',
	`ok` TINYINT(1) NOT NULL DEFAULT '0',
	`data` TEXT NOT NULL,
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


$queries[]="CREATE TABLE IF NOT EXISTS " . $app['table_prefix'] . "users(
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active_date` timestamp NULL,
  `group_id` int unsigned NOT NULL DEFAULT '0',
  `first_name` varchar(128) NOT NULL DEFAULT '',
  `last_name` varchar(128) NOT NULL DEFAULT '',
  `username` varchar(128) NOT NULL DEFAULT '',
  `pass` varchar(128) NOT NULL DEFAULT '',
  `language_code` varchar(8) NOT NULL DEFAULT 'en',
  `email` varchar(128) NOT NULL DEFAULT '',
  `phone` varchar(16) NOT NULL DEFAULT '',
  `current_step` varchar(128) NOT NULL DEFAULT '',
  `offset_DST_seconds` int NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `blocked` TINYINT(1) NOT NULL DEFAULT '0',
  `block_date_start` timestamp NULL,
  `block_date_end` timestamp NULL,
  `activated` TINYINT(1) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   UNIQUE (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";


$queries[] ="CREATE TABLE IF NOT EXISTS ".$app['table_prefix']."user_sessions(
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int UNSIGNED NOT NULL DEFAULT '0',
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`token` varchar(128) NOT NULL DEFAULT '',
	`user_info` text,
	PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

/* $queries[]="CREATE TABLE IF NOT EXISTS " . $app['table_prefix'] . "directors(
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(128) NOT NULL DEFAULT '',
  `last_name` varchar(128) NOT NULL DEFAULT '',
  `pictures` varchar(128) NOT NULL DEFAULT '',
  `bio` text NOT NULL,
  `rating` FLOAT NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$queries[]="CREATE TABLE IF NOT EXISTS " . $app['table_prefix'] . "actors(
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(128) NOT NULL DEFAULT '',
  `last_name` varchar(128) NOT NULL DEFAULT '',
  `pictures` varchar(128) NOT NULL DEFAULT '',
  `rating` FLOAT NOT NULL DEFAULT '0',
  `bio` text NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$queries[]="CREATE TABLE IF NOT EXISTS " . $app['table_prefix'] . "genres(
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
   PRIMARY KEY (`id`),
   UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"; */

$queries[]="CREATE TABLE IF NOT EXISTS " . $app['table_prefix'] . "movies(
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `friendlyURL` varchar(180) NOT NULL DEFAULT '',
  `title` varchar(128) NOT NULL DEFAULT '',
  `slogan` varchar(256) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `cover` varchar(128) NOT NULL DEFAULT '',
  `pictures` text NOT NULL,
  `year` INT NOT NULL DEFAULT '0',
  `rating` FLOAT NOT NULL DEFAULT '0',
  `price` FLOAT NOT NULL DEFAULT '0',
  `genres` varchar(128) NOT NULL DEFAULT '',
  `directors` varchar(128) NOT NULL DEFAULT '',
  `actors` varchar(128) NOT NULL DEFAULT '',
  `duration` FLOAT NOT NULL DEFAULT '0',
  `trailers` text NOT NULL,
  `seen` int unsigned NOT NULL DEFAULT '0',
  `predelete` TINYINT(1) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   UNIQUE (`friendlyURL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";


$queries[]="CREATE TABLE IF NOT EXISTS " . $app['table_prefix'] . "booking_seats (
	id int unsigned NOT NULL AUTO_INCREMENT,
	booking_id int unsigned NOT NULL DEFAULT '0',
	movie_id int unsigned NOT NULL DEFAULT '0',
	date DATE,
	time TIME,
    seat_number VARCHAR(5),
	status TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (id, booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
//status ENUM('vacant', 'sold') DEFAULT 'vacant',

$queries[]="CREATE TABLE IF NOT EXISTS " . $app['table_prefix'] . "bookings(
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `book_date` timestamp NULL,
  `movie_id` int unsigned NOT NULL DEFAULT '0',
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `seats_count` INT NOT NULL DEFAULT '0',
  `price` FLOAT NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   FOREIGN KEY (user_id) REFERENCES ". $app['table_prefix'] . "users(id),
   FOREIGN KEY (movie_id) REFERENCES ". $app['table_prefix'] . "movies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$queries[]="CREATE TABLE IF NOT EXISTS " . $app['table_prefix'] . "OTPs(
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valid_till` int unsigned NOT NULL DEFAULT '0',
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `code` varchar(128) NOT NULL DEFAULT '',
  `email` varchar(128) NOT NULL DEFAULT '',
  `activated` TINYINT(1) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   FOREIGN KEY (user_id) REFERENCES ". $app['table_prefix'] . "users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";


// --- Install ---
if ($install) {
	for ($i = 0; $i < count($queries); $i++) { echo "Making request [$i] ..."; make_sql($queries[$i]); }
}

// --- Functions ---
function make_sql($sqlr){
	global $app;
	if ($sqlr=='') return false; 
	$sql=mysqli_query($app['conn'],$sqlr); 
	if (!$sql) { echo '<span class="err">[ERR]'. mysqli_error($conn).'</span><br>'.$sqlr.'<br><br>'; } else { echo '<span class="ok">[OK]</span><br>'; }
}

?>