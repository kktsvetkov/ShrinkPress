<?php

namespace ShrinkPress\Build\Entity\Files;

use ShrinkPress\Build\Entity;

class WordPress_PHP Extends PHP_File
{
	const factory_map = array(
		':external' => External_Lib::class,
		'wp-admin/' => WP_Admin::class,
		);

	static function factory($filename)
	{
		$filename = (string) $filename;
	}
}
