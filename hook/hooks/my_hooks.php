<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	'my_hook' 	=>	function ($number) {
		echo Debug::vars($number);
		$number++;
	}
);