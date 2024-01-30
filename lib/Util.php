<?php

namespace form;

/**
 * Email utility methods.
 */
class Util {
	/**
	 * Converts an `[email, name]` array into a `name <email>` string.
	 * Leaves strings untouched to pass-through as email-only.
	 */
	public static function format_user ($user) {
		if (is_array ($user)) {
			return sprintf ('%s <%s>', $user[1], $user[0]);
		}
		return $user;
	}
	
	/**
	 * Gets the default email from email/name from the global settings
	 * should it be omitted from the `send()` parameters.
	 */
	public static function get_default_from () {
		$config = conf ('Mailer');
		
		return [
			($config['email_from'] !== 'default') ? $config['email_from'] : conf ('General', 'email_from'),
			($config['email_name'] !== 'default') ? $config['email_name'] : conf ('General', 'site_name')
		];
	}
}