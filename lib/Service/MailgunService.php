<?php

namespace form\Service;

use Envconf;
use Exception;
use form\Util;

/**
 * Sends emails through the Mailgun service, which bypasses the need to
 * install postfix or another SMTP service on the web server.
 */
class MailgunService {
	/**
	 * Send an email. Wrap it in the usual try/catch block to catch errors.
	 * Parameters are the same as for `Mailer::send()`.
	 */
	public static function send ($data) {
		$api_key = Envconf::form ('Service', 'mailgun_api_key');
		$base_url = Envconf::form ('Service', 'mailgun_base_url');
		
		$url = $base_url . '/messages';
		
		if (! isset ($data['from'])) {
			$data['from'] = Util::get_default_from ();
		}
		
		$data['from'] = Util::format_user ($data['from']);
		$data['to'] = Util::format_user ($data['to']);
		if (isset ($data['reply_to'])) {
			$data['h:Reply-To'] = Util::format_user ($data['reply_to']);
			unset ($data['reply_to']);
		}

		$q = http_build_query ($data);
		
		$ch = curl_init ();

		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $q);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_USERPWD, 'api:' . $api_key);
		
		$out = curl_exec ($ch);
		
		if ($out === false) {
			error_log ('Mailgun cURL request failure: ' . curl_error ($ch));
			throw new Exception ('Email server communication error');
		}

		curl_close ($ch);
		
		$res = json_decode ($out, true);
		if ($res == null) {
			error_log ('Failed to parse Mailgun response: ' . $res);
			throw new Exception ('Email server communication error');
		}

		if (! isset ($res['id'])) {
			error_log ('Mailgun returned no message ID: ' . $out);
			throw new Exception ('Email server communication error');
		}

		return true;
	}
}
