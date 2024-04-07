<?php 

	// ==================== LANGUAGE FUNCTION ====================

	if (! function_exists('__')) {
		function __($key = null) {
			global $app;
			if (isset($app['lang']) && $app['lang']=='en') return $key;
			if (is_null($key)) return $key;
			return trans($key);
		}
	}
	function trans($key) {
		global $_translations;
		if (!isset($_translations)) return $key;
		if (!is_array($_translations)) return $key;
		if (empty($_translations)) return $key;
		if (isset($_translations[$key])) return $_translations[$key];
		return $key;
	}
	
	// ==================== DIFF FUNCTION ====================

	function GetInTranslit($string) {
		$replace=array(
			"'"=>"",
			"`"=>"",
			"а"=>"a","А"=>"a",
			"б"=>"b","Б"=>"b",
			"в"=>"v","В"=>"v",
			"г"=>"g","Г"=>"g",
			"д"=>"d","Д"=>"d",
			"е"=>"e","Е"=>"e",
			"ж"=>"zh","Ж"=>"zh",
			"з"=>"z","З"=>"z",
			"и"=>"i","И"=>"i",
			"й"=>"y","Й"=>"y",
			"к"=>"k","К"=>"k",
			"л"=>"l","Л"=>"l",
			"м"=>"m","М"=>"m",
			"н"=>"n","Н"=>"n",
			"о"=>"o","О"=>"o",
			"п"=>"p","П"=>"p",
			"р"=>"r","Р"=>"r",
			"с"=>"s","С"=>"s",
			"т"=>"t","Т"=>"t",
			"у"=>"u","У"=>"u",
			"ф"=>"f","Ф"=>"f",
			"х"=>"h","Х"=>"h",
			"ц"=>"c","Ц"=>"c",
			"ч"=>"ch","Ч"=>"ch",
			"ш"=>"sh","Ш"=>"sh",
			"щ"=>"sch","Щ"=>"sch",
			"ъ"=>"","Ъ"=>"",
			"ы"=>"y","Ы"=>"y",
			"ь"=>"","Ь"=>"",
			"э"=>"e","Э"=>"e",
			"ю"=>"yu","Ю"=>"yu",
			"я"=>"ya","Я"=>"ya",
			"і"=>"i","І"=>"i",
			"ї"=>"yi","Ї"=>"yi",
			"є"=>"e","Є"=>"e"
		);

		$str=iconv("UTF-8","UTF-8//IGNORE",strtr($string,$replace));
		$str=trim(strtolower($str));
		$str =preg_replace('![^\w\d\s]*!','',$str);
		$str = preg_replace("/[ ]+/ui", "_", $str);
		return $str;
	}

?>