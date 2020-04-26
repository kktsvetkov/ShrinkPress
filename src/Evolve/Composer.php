<?php

namespace ShrinkPress\Reframe\Evolve;

class Composer
{
	const vendors = 'shrinkpress-vendors';

	const source = array(
		'name' => 'shrinkpress/shrinkpress',
		'description' => 'ShrinkPress: Break WordPress Apart',
		'type' => 'project',
		'license' => 'GPL-2.0-or-later',
		'require' => array(
			'php' => '>=7.0.0',
			),
		'config' =>  array(
			'vendor-dir' => self::vendors,
			),
		'autoload' => array(
			'psr-4' => array(),
			),
	);

	static $psr4 = array();

	static function addPsr4($namespace, $folder)
	{
		self::$psr4[ $namespace ] = $folder;
	}

	static function updateComposer()
	{
		$data = self::source;
		$data['autoload']['psr-4'] = (object) self::$psr4;

		file_put_contents(
			'composer.json',
			json_encode(
       				$data,
       				JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
       			));

		ComposerPhar::dumpautoload();
	}

	static function plantComposer()
	{
		self::updateComposer();

		$code = file_get_contents('wp-settings.php');
		$code = Code::injectCode($code, array(
			'define', '(', "'WPINC'", ',', "'wp-includes'", ')', ';'
			), join("\n", array(
			'',
			'',
			'/** @see shrinkpress */',
			'require ABSPATH . \'/'
				. self::vendors
				. '/autoload.php\';',
			)));
		file_put_contents('wp-settings.php', $code);
	}

	static function wipeComposer()
	{
		shell_exec('rm composer.json');
		shell_exec('rm -rf ' . self::vendors);
	}
}
