<?php

namespace ShrinkPress\Build\Condense;

use ShrinkPress\Build\Project\Storage;

class ProtoPackages
{
	use Instance;

	function setStorage(Storage\StorageAbstract $storage)
	{
		$this->storage = $storage;
	}

	protected $packages = array();

	private static function classify($string)
	{
		$string = pathinfo($string, PATHINFO_FILENAME);

		$string = join('_', array_map('ucfirst', explode('-', $string)));
		$string = join('\\', array_map('ucfirst', explode('\\', $string)));

		$string = str_replace('Wordpress\Wp_Includes', 'Wp_Includes', $string);
		$string = str_replace('Wordpress\Wp_Admin', 'Wp_Admin', $string);

		if (!$string)
		{
			throw new \Exception;
		}

		return $string;
	}

	function getPackages()
	{
		if (empty($this->packages))
		{
			$this->packages = array();

			$all = $this->storage->getFunctions();
			foreach ($all as $name)
			{
				$func = $this->storage->readFunction($name);

				$file = $func->fileOrigin;
				if (!$file)
				{
					continue;
				}

				$class = self::classify($file);

				$file = 'wordpress/' . $file;
				$dir = str_replace('/', '\\', dirname($file));
				$namespace = self::classify($dir);

				// echo "> {$name}() at {$file}\n";
				// echo "< {$namespace}\\{$class}::{$name}()\n\n";

				$this->packages[ $namespace ][ $class ][] = array($name);
			}
		}

		// foreach ($this->packages as $namespace => $classes)
		// {
		// 	$json = __DIR__ . '/../../packages/proto/'
		// 		. md5($namespace) . '.json';
		//
		// 	file_put_contents($json, json_encode(array(
		// 		$namespace => $classes
		// 		), JSON_PRETTY_PRINT));
		// }

		return array_keys($this->packages);
	}

	function definition($package)
	{
		if (empty($this->packages[ $package ]))
		{
			throw new \InvalidArgumentException(
				"Unable to find package '{$package}'"
			);
		}

		return $this->packages[ $package ];
	}
}
