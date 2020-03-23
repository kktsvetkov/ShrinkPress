<?php

namespace ShrinkPress\Build\Disintegrate;

class Packages
{
	static protected $packages = array();

	static function packages()
	{
		if (empty(self::$packages))
		{
			self::$packages = array();

			$dir = new \DirectoryIterator( __DIR__ . '/../../packages/' );
			foreach ($dir as $found)
			{
				if ($found->isDot())
				{
					continue;
				}

				$key = $found->getBasename(
					'.' . $found->getExtension()
					);
				self::$packages[ $key ] = $found->getRealpath();
			}
		}

		return array_keys(self::$packages);
	}

	static function definition($package)
	{
		if (empty(self::$packages[ $package ]))
		{
			throw new \InvalidArgumentException(
				"Unable to find package '{$package}'"
			);
		}

		if (!file_exists( self::$packages[ $package ] ))
		{
			throw new \UnexpectedValueException(
				"Missing file for package '{$package}': "
					. self::$packages[ $package ]
			);
		}

		return json_decode(
			file_get_contents(self::$packages[ $package ]),
			true);
	}
}
