<?php

namespace form\Service;

use Mailer;

/**
 * Sends emails through Elefant's default Mailer service.
 */
class MailerService {
	/**
	 * Send an email.
	 */
	public static function send ($data) {
		return Mailer::send ($data);
	}
}
