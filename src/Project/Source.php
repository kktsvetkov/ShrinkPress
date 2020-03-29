<?php

namespace ShrinkPress\Build\Project;

use ShrinkPress\Build\Find;
use ShrinkPress\Build\Verbose;

class Source
{
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

	function basedir()
	{
		return $this->basedir;
	}

	/**
	* Converts a WP project filename into a real one
	*
	* @param string $filename
	* @return string
	*/
	function local($filename)
	{
		return $this->basedir . ltrim($filename, '/');
	}

	/**
	* Converts a real filename into a WP project filename
	*
	* @param string $filename
	* @return string
	*/
	function remote($filename)
	{
		if (0 === strpos($filename, $this->basedir))
		{
			$filename = substr($filename, strlen($this->basedir));
		}

		return $filename;
	}

	function exists($filename)
	{
		$local = $this->local($filename);
		return file_exists($local);
	}

	function read($filename)
	{
		$local = $this->local($filename);
		if (!file_exists($local))
		{
			throw new \InvalidArgumentException(
				"Argument \$filename '{$filename}' does not exist"
					. " (in {$this->basedir})"
			);
		}

		return file_get_contents( $local );
	}

	function write($filename, $contents)
	{
		Verbose::log("Write: {$filename}", 1);

		$local = $this->local($filename);
		$dir = dirname($local);
		if (!file_exists($dir))
		{
			mkdir($dir, 0777, true);
		}

		return file_put_contents($local, $contents);
	}

	function unlink($filename)
	{
		$local = $this->local($filename);
		if (!file_exists($local))
		{
			throw new \InvalidArgumentException(
				"Argument \$filename '{$filename}' does not exist"
					. " (in {$this->basedir})"
			);
		}

		return unlink( $local );
	}

}
