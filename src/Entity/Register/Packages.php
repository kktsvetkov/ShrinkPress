<?php

namespace ShrinkPress\Build\Entity\Register;

use ShrinkPress\Build\Entity\Files\File_Entity;
use ShrinkPress\Build\Entity\Stash;
use ShrinkPress\Build\Assist;

class Packages extends Register_Abstract
{
	use Assist\Instance;

	function getPackages()
	{
		return $this->register();
	}

	function getPackageNames()
	{
		$keys = array();
		foreach ($this->register as $key => $subs)
		{
			$keys[ $key ] = array_keys( $subs );
		}

		return $keys;
	}

	function addPackage(File_Entity $file)
	{
		if (empty($file->docPackage))
		{
			return $this;
		}

		if (!isset($this->register[ $file->docPackage ]))
		{
			$this->register[ $file->docPackage ] = array();
		}
		if (!isset($this->register[ $file->docPackage ][ $file->docSubPackage ]))
		{
			$this->register[ $file->docPackage ][ $file->docSubPackage ] = array();
		}

		$filename = $file->fileName();
		if (!in_array($filename, $this->register[ $file->docPackage ][ $file->docSubPackage ]))
		{
			$this->register[ $file->docPackage ][ $file->docSubPackage ][] = $filename;
		}

		return $this;
	}

	function getPackage($package, $subpackage = null)
	{
		$package = (string) $package;
		if (empty($this->register[ $package ]))
		{
			return false;
		}

		if (null == $subpackage)
		{
			return $this->register[ $package ];
		}

		$subpackage = (string) $subpackage;
		if (empty($this->register[ $package ][ $subpackage ]))
		{
			return false;
		}

		return $this->register[ $package ][ $subpackage ];
	}

	function save()
	{
		$stash = Stash::instance();
		$stash->write(
			$this->stashFilename(),
			json_encode(
				$this->jsonSerialize(),
				self::json_encode_options
				)
			);
	}

	function jsonSerialize()
	{
		return $this->register;
	}
}
