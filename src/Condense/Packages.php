<?php

namespace ShrinkPress\Build\Condense;

use ShrinkPress\Build\Assist;

class Packages
{
	use Assist\Instance;

	protected $packages = array();

	function getPackages()
	{
		if (empty($this->packages))
		{
			$this->packages = array();

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
				$this->packages[ $key ] = $found->getRealpath();
			}
		}

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

		if (!file_exists( $this->packages[ $package ] ))
		{
			throw new \UnexpectedValueException(
				"Missing file for package '{$package}': "
					. $this->packages[ $package ]
			);
		}

		return json_decode(
			file_get_contents($this->packages[ $package ]),
			true);
	}
}
