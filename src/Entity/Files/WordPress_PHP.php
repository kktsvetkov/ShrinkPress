<?php

namespace ShrinkPress\Build\Entity\Files;

use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Entity;

class WordPress_PHP Extends PHP_File
{
	const factory_map = array(
		':external' => External_Lib::class,
		'wp-includes/compat.php' => Compat::class,
		'wp-includes/default-filters.php' => Default_Filters::class,
		'wp-includes/pluggable.php' => Pluggable::class,
		'wp-includes/pluggable-deprecated.php' => Pluggable_Deprecated::class,
		'wp-includes/blocks/' => WP_Block::class,
		'wp-includes/widgets/' => WP_Widget::class,
		'wp-admin/wp-includes/' => WP_Admin_Include::class,
		'wp-admin/' => WP_Admin::class,
		'wp-includes/' => WP_Include::class,
		);

	static function factory($filename)
	{
		$filename = (string) $filename;

		foreach (self::factory_map as $file_mask => $file_entity_class)
		{
			if (':external' == $file_mask)
			{
				if (Assist\External::isExternal( $filename ))
				{
					return new External_Lib( $filename );
				}
				continue;
			}

			if (0 === strpos($filename, $file_mask))
			{
				return new $file_entity_class( $filename );
			}
		}

		return new WordPress_PHP( $filename );
	}
}
