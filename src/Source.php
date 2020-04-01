<?php

namespace ShrinkPress\Build;

use ShrinkPress\Build\Verbose;

class Source
{
	protected $basedir;

	/**
	* Opens $basedir folder for reading the WordPress files from
	*
	* @param string $basedir filepath to the WordPress folder
	* @throws \InvalidArgumentException
	*/
	function __construct($basedir)
	{
		if (!is_dir($basedir))
		{
			throw new \InvalidArgumentException(
				"Argument \$basedir must be an existing folder, '{$basedir}' is not"
			);
		}

		$this->basedir = rtrim($basedir, '/') . '/';
	}

	/**
	* Get the sourde folder
	* @return string
	*/
	function basedir()
	{
		return $this->basedir;
	}

	/**
	* Converts a relative project filename into a full filepath
	*
	* @param string $filename
	* @return string
	*/
	function full($filename)
	{
		return $this->basedir . ltrim($filename, '/');
	}

	/**
	* Converts a full filepath into a source-relative local filename
	*
	* @param string $filename
	* @return string
	*/
	function local($filename)
	{
		if (0 === strpos($filename, $this->basedir))
		{
			$filename = substr($filename, strlen($this->basedir));
		}

		return $filename;
	}

	/**
	* Checks if a file exists in the source folder
	*
	* @param string $filename
	* @return boolean
	*/
	function exists($filename)
	{
		$full = $this->full($filename);
		return file_exists( $full );
	}

	/**
	* Reads contents of a file from the source folder
	*
	* @param string $filename
	* @return boolean
	*/
	function read($filename)
	{
		$full = $this->full($filename);
		if (!file_exists( $full ))
		{
			throw new \InvalidArgumentException(
				"Argument \$filename '{$filename}' does not exist"
					. " (in {$this->basedir})"
			);
		}

		return file_get_contents( $full );
	}

	/**
	* Writes contents to a file in the source folder
	*
	* @param string $filename
	* @return boolean
	*/
	function write($filename, $contents)
	{
		Verbose::log("Write: {$filename}", 1);

		$full = $this->full($filename);
		$dir = dirname( $full );
		if (!file_exists($dir))
		{
			mkdir($dir, 02777, true);
		}

		return file_put_contents($full, $contents);
	}

	/**
	* Deletes a file from the source folder
	*
	* @param string $filename
	* @return boolean
	*/
	function unlink($filename)
	{
		$full = $this->full($filename);
		if (!file_exists( $full ))
		{
			throw new \InvalidArgumentException(
				"Argument \$filename '{$filename}' does not exist"
					. " (in {$this->basedir})"
			);
		}

		return unlink( $full );
	}

}
