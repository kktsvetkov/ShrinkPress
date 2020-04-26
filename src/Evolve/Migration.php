<?php

namespace ShrinkPress\Reframe\Evolve;

class Migration
{
	static function getFunction(array $f)
	{
		$function = (string) $f['function'];
		if (empty(self::migrateFunctions[ $function ]))
		{
			// dummy proto packages based on filename
			//
			return Proto::getFunction($f);
		}

		$m = self::migrateFunctions[ $function ];
		return array(
			'method' => $m[0],
			'class' => $m[1],
			'namespace' => $m[2],
			'full' => "{$m[2]}\\{$m[1]}::{$m[0]}"
		);
	}

	/**
	* method, class, namespace
	*/
	const migrateFunctions = array(
		'export_add_js' => array('add_js', 'Export', 'ShrinkPress\\Admin'),
		'get_cli_args' => array('get_cli_args', 'Console', 'ShrinkPress\\Admin'),
		'wp_nav_menu_max_depth' => array('max_depth', 'Menu', 'ShrinkPress\\Admin'),
		'do_activate_header' => array('activate_header', 'Activate', 'ShrinkPress\\Activate'),
	);
}
