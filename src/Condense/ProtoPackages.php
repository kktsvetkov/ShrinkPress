<?php

namespace ShrinkPress\Build\Condense;

use ShrinkPress\Build\Project\Storage;
use ShrinkPress\Build\Verbose;

class ProtoPackages
{
	use \ShrinkPress\Build\Assist\Instance;

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

		$string = str_replace('Wp_', '', $string);
		$string = str_replace('Wordpress', 'WordPress', $string);

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
			Verbose::log('Proto packages...', 2);
			$this->packages = array();

			$all = $this->storage->getFunctions();
			foreach ($all as $name)
			{
				$func = $this->storage->readFunction($name);

				if (!$file = $func->fileOrigin)
				{
					continue;
				}

				$class = self::classify($file);

				$file = 'wordpress/' . $file;
				$dir = str_replace('/', '\\', dirname($file));
				$namespace = self::classify($dir);

				$n = explode('\\', $namespace);
				$package = array_shift($n);

				if ($n)
				{
					$class = join('\\', $n) . '\\' . $class;
				}
				unset($n, $namespace);

				// echo "> {$name}() at {$file}\n";
				// echo "< {$package}\\{$class}::{$name}()\n\n";

				$this->packages[ $package ][ $class ][] = array($name);
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

		return array(
			$package => $this->packages[ $package ]
		);
	}
}
