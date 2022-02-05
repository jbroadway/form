<?php

namespace form;

use User;

class Filter {
	public static function label ($str) {
		$str = str_replace ('_', ' ', $str);
		return str_pad ($str, 32, ' ', STR_PAD_RIGHT);
	}
	
	public static function app_name ($name) {
		$total = Unread::total (User::val ('id'));
		return $name . ' (' . $total . ' ' . __ ('unread') . ')';
	}
}
