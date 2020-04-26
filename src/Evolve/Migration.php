<?php

namespace ShrinkPress\Reframe\Evolve;

class Migration
{
	static function getFunction(array $f)
	{
		return array($f['function'], 'Uno', 'Due', 'full' => 'Uno\\Due::' . $f['function']);

		$function = (string) $f['function'];
		if (empty(self::migrateFunctions[ $function ]))
		{
			return false;
		}

		$m = self::migrateFunctions[ $function ];
		return array(
			'method' => $m[0],
			'classs' => $m[1],
			'namespace' => $m[2],
			'full' => "{$m[2]}\\{$m[1]}::{$m[0]}"
		);
	}

	/**
	* method, class, namespace
	*/
	const migrateFunctions = array(
		'export_add_js' => array('add_js', 'Export', 'ShrinkPress\\Admin'),
		'do_activate_header' => array('activate_header', 'Activate', 'ShrinkPress\\Activate'),
	);
}
