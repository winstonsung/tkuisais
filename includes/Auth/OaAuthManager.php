<?php

namespace Isais\Auth;

ini_set('default_charset', 'utf-8');

class OaAuthManager {
	public static function login($username, $password)
	{
		$is_windows = false;
		$operating_system = php_uname();
		$token = null;

		if (strpos($operating_system, 'Windows') !== false) {
			$is_windows = true;
		}

		$curl_command = 'curl ' .
			'--insecure ' .
			'--location \'https://oa.tku.edu.tw/XOAEntry.nsf/EntryForm?OpenForm\' ' .
			'--header \'Cookie: LoginCode=' . $username . '\'';

		if ($is_windows) {
			$curl_command = 'curl ' .
				'--insecure ' .
				'--location "https://oa.tku.edu.tw/XOAEntry.nsf/EntryForm?OpenForm" ' .
				'--header "Cookie: LoginCode=' . $username . '"';
		}

		$response = shell_exec($curl_command);

		preg_match(
			'/_doClick\(\'([^\']+)\'/',
			$response,
			$matches
		);

		$click = $matches[1];

		$curl_command = 'curl ' .
			'--insecure ' .
			'--request POST ' .
			'--location \'https://oa.tku.edu.tw/XOAEntry.nsf/EntryForm?OpenForm=&Seq=1\' ' .
			'--header \'Content-Type: application/x-www-form-urlencoded; charset=utf-8\' ' .
			'--data-urlencode \'__Click=' . $click . '\' ' .
			'--data-urlencode \'Action=Login\' ' .
			'--data-urlencode \'SaveOptions=0\' ' .
			'--data-urlencode \'LoginCode=' . $username . '\' ' .
			'--data-urlencode \'IDCode=' . $password . '\'';

		if ($is_windows) {
			$curl_command = 'curl ' .
				'--insecure ' .
				'--request POST ' .
				'--location "https://oa.tku.edu.tw/XOAEntry.nsf/EntryForm?OpenForm=&Seq=1" ' .
				'--header "Content-Type: application/x-www-form-urlencoded; charset=utf-8" ' .
				'--data-urlencode "__Click=' . $click . '" ' .
				'--data-urlencode "Action=Login" ' .
				'--data-urlencode "SaveOptions=0" ' .
				'--data-urlencode "LoginCode=' . $username . '" ' .
				'--data-urlencode "IDCode=' . $password . '"';
			}

		$response = shell_exec($curl_command);

		if (strpos($response, '<b>' . $username . '</b>') === false) {
			return false;
		}

		$curl_command = 'curl ' .
			'--insecure ' .
			'--location \'https://oa.tku.edu.tw/XOAEntry.nsf/Reconfirm?OpenForm=&user=' . $username . '&=\' ' .
			'--header \'Cookie: LoginCode=\'';

		if ($is_windows) {
			$curl_command = 'curl ' .
				'--insecure ' .
				'--location "https://oa.tku.edu.tw/XOAEntry.nsf/Reconfirm?OpenForm=&user=' . $username . '&=" ' .
				'--header "Cookie: LoginCode="';
		}

		$response = shell_exec($curl_command);

		$curl_command = 'curl ' .
			'-i ' .
			'--insecure ' .
			'--location \'https://oa.tku.edu.tw/names.nsf?Login=&Username=person&Password=personlogin&RedirectTo=%2FXOAPerson.nsf%2FPersonForm%3FOpenForm\' ' .
			'--header \'Cookie: LoginCode=' . $username . '\'';

		if ($is_windows) {
			$curl_command = 'curl ' .
				'-i ' .
				'--insecure ' .
				'--location "https://oa.tku.edu.tw/names.nsf?Login=&Username=person&Password=personlogin&RedirectTo=%2FXOAPerson.nsf%2FPersonForm%3FOpenForm" ' .
				'--header "Cookie: LoginCode=' . $username . '"';
		}

		$response = shell_exec($curl_command);

		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);

		foreach ($matches[1] as $item) {
			$cookie = explode('=', $item, 2);

			if ($cookie[0] === 'DomAuthSessId') {
				$token = $cookie[1];
			}
		}

		$curl_command = 'curl ' .
			'--insecure ' .
			'--location \'https://oa.tku.edu.tw/XOAPerson.nsf/PersonForm?OpenForm=\' ' .
			'--header \'Cookie: LoginCode=' . $username . '; DomAuthSessId=' . $token . '\'';

		if ($is_windows) {
			$curl_command = 'curl ' .
				'--insecure ' .
				'--location "https://oa.tku.edu.tw/XOAPerson.nsf/PersonForm?OpenForm=" ' .
				'--header "Cookie: LoginCode=' . $username . '; DomAuthSessId=' . $token . '"';
		}

		$response = shell_exec($curl_command);

		preg_match(
			'/\] ([^(]+)\(' . $username . '/',
			$response,
			$matches
		);

		$user_name_label = $matches[1];

		return array(
			'username' => $username,
			'user_name_label' => $user_name_label,
			'password' => $password,
			'token' => $token,
		);
	}
}
