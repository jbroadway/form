<?php

namespace form;

class Filter {
	public static function label ($str) {
		$str = str_replace ('_', ' ', $str);
		return str_pad ($str, 32, ' ', STR_PAD_RIGHT);
	}
}
