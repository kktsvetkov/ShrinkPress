<?php

namespace ShrinkPress\Reframe\Assist;

/**
* Umbrella, folder proxy: reads and writes files and folders
* from under a $basedir, e.g. under the umbrealla
*/
class Umbrella
{
	protected $basedir;

	/**
	* Opens $basedir folder under the umbrella
	*
	* @param string $basedir filepath to root folder for the umbrella
	* @throws \InvalidArgumentException
	*/
	function __construct($basedir)
	{
		$basedir = (string) $basedir;
		$this->basedir = rtrim($basedir, '/') . '/';
	}

	/**
	* Get the umbrealla root folder
	* @return string
	*/
	function basedir()
	{
		return $this->basedir;
	}

	/**
	* Converts a relative filename into a full filepath
	*
	* @param string $filename
	* @return string
	*/
	function full($filename)
	{
		return $this->basedir . ltrim($filename, '/');
	}

	/**
	* Converts a full filepath into a relative (under the umbrealla, local) filename
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
	* Checks if a file exists under the umbrella folder
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
	* Reads contents of a file from under the umbrella folder
	*
	* @param string $filename
	* @return boolean
	*/
	function read($filename)
	{
		$full = $this->full( $filename );
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
	* Writes contents to a file under the umbrella folder
	*
	* @param string $filename
	* @return boolean
	*/
	function write($filename, $contents)
	{
		$full = $this->full($filename);
		$dir = dirname( $full );
		if (!file_exists($dir))
		{
			mkdir($dir, 02777, true);
		}

		return file_put_contents($full, $contents);
	}

	/**
	* Deletes a file from under the umbrella folder
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
