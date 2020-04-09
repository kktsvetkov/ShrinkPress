<?php

namespace ShrinkPress\Build\Assist;

class External
{
	/**
	* Is it an external PHP library used by WordPress ?
	* @param string $filepath
	* @return boolean
	*/
	static function isInternal($filepath)
	{
		$filepath = (string) $filepath;
		return !empty(self::external[ $filepath ]);
	}

	const external = array(
		'wp-includes/atomlib.php' => 'AtomLib',
		'wp-includes/SimplePie/' => 'SimplePie',
	);
}
