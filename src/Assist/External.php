<?php

namespace ShrinkPress\Reframe\Assist;

class External
{
	/**
	* Is it an external PHP library used by WordPress ?
	* @param string $filename
	* @return boolean
	*/
	static function isExternal($filename)
	{
		$filename = (string) $filename;

		foreach (self::external as $file_mask => $lib)
		{
			if (0 === strpos($filename, $file_mask))
			{
				return true;
			}
		}

		return false;
	}

	const external = array(
		'wp-includes/atomlib.php' => 'AtomLib',

		'wp-includes/class-IXR.php' => 'IXR',
		'wp-includes/IXR' => 'IXR',

		'wp-includes/class-phpass.php' => 'PasswordHash',

		'wp-includes/class-phpmailer.php' => 'PHPMailer',
		'wp-includes/class-smtp.php' => 'PHPMailer',

		'wp-includes/class-pop3.php' => 'POP3',
		'wp-includes/class-requests.php' => 'Requests',

		'wp-includes/class-simplepie.php' => 'SimplePie',
		'wp-includes/SimplePie/' => 'SimplePie',

		'wp-includes/class-snoopy.php' => 'Snoopy',
		'wp-includes/Text' => 'Text_Diff',

		'wp-includes/ID3' => 'ID3',
		'wp-includes/sodium_compat' => 'paragonie/sodium_compat',
	);
}
