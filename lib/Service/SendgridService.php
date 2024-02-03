<?php

namespace form\Service;

use Envconf;
use Exception;
use form\Util;

/**
 * Sends emails through the Sendgrid service, which bypasses the need to
 * install postfix or another SMTP service on the web server.
 */
class SendgridService {
	/**
	 * Send an email. Wrap it in the usual try/catch block to catch errors.
	 * Parameters are the same as for `Mailer::send()`.
	 */
	public static function send ($data) {
		$api_key = Envconf::form ('Service', 'sendgrid_api_key');
		
		$url = 'https://api.sendgrid.com/v3/mail/send';
		
		if (! isset ($data['from'])) {
			$data['from'] = Util::get_default_from ();
		}
		
		// Transform parameters

		if (is_array ($data['from'])) {
			$email = $data['from'][0];
			$name = $data['from'][1];
			$data['from'] = [
				'email' => $email,
				'name' => $name
			];
		} else {
			$email = $data['from'];
			$data['from'] = ['email' => $email];
		}

		if (is_array ($data['to'])) {
			$email = $data['to'][0];
			$name = $data['to'][1];
			$data['personalizations'] = [
				[
					'to' => [
						[
							'email' => $email,
							'name' => $name
						]
					]
				]
			];
		} else {
			$email = $data['to'];
			$data['personalizations'] = [
				[
					'to' => [
						[
							'email' => $email
						]
					]
				]
			];
		}
		unset ($data['to']);

		if (isset ($data['reply_to'])) {
			if (is_array ($data['reply_to'])) {
				$email = $data['reply_to'][0];
				$name = $data['reply_to'][1];
				$data['reply_to'] = [
					'email' => $email,
					'name' => $name
				];
			} else {
				$email = $data['reply_to'];
				$data['reply_to'] = ['email' => $email];
			}
		}

		$data['content'] = [];

		if (isset ($data['text'])) {
			if ($data['text'] !== '') {
				$data['content'][] = ['type' => 'text/plain', 'value' => $data['text']];
			}
			unset ($data['text']);
		}

		if (isset ($data['html'])) {
			if ($data['html'] !== '') {
				$data['content'][] = ['type' => 'text/html', 'value' => $data['html']];
			}
			unset ($data['html']);
		}

		$q = json_encode ($data);
		
		$ch = curl_init ();

		curl_setopt ($ch, CURLOPT_HTTPHEADER, [
			'Authorization: Bearer ' . $api_key,
			'Content-Type: application/json'
		]);

		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $q);
		
		$out = curl_exec ($ch);

		if ($res === false || curl_errno ($ch)) {
			error_log ('Sendgrid cURL request failure: ' . curl_error ($ch));
			throw new Exception ('Email server communication error');
		}

		$info = curl_getinfo ($ch);

		if ((int) $info['http_code'] != 202) {
			error_log ('Sendgrid error response code: ' . $info['http_code']);
			throw new Exception ('Email server communication error');
		}

		curl_close ($ch);

		return true;
	}
}
