<?php

namespace ShrinkPress\Build\Project;

use ShrinkPress\Build\Find;
use ShrinkPress\Build\Verbose;

class Source
{
	protected $storage;

	/**
	* Opens $source folder for reading the WordPress files from
	*
	* @param string $source filepath to the WordPress folder
	* @param ShrinkPress\Build\Storage\StorageAbstract $storage
	* @throws \InvalidArgumentException
	*/
	function __construct($source, Storage\StorageAbstract $storage)
	{
		if (!is_dir($source))
		{
			throw new \InvalidArgumentException(
				"Argument \$source must be an existing folder, '{$source}' is not"
			);
		}

		$this->source = rtrim($source, '/') . '/';
		$this->storage = $storage;
	}

	/**
	* Converts a WP project filename into a real one
	*
	* @param string $filename
	* @return string
	*/
	protected function local($filename)
	{
		return $this->source . ltrim($filename, '/');
	}

	/**
	* Converts a real filename into a WP project filename
	*
	* @param string $filename
	* @return string
	*/
	protected function remote($filename)
	{
		if (0 === strpos($filename, $this->source))
		{
			$filename = substr($filename, strlen($this->source));
		}

		return $filename;
	}

	/**
	* Scans the $folder for WordPress PHP files
	*
	* @param string $folder a WP project folder, e.g. "wp-includes/"
	* @throws \InvalidArgumentException
	*/
	function scan($folder = '')
	{
		$local = $this->local($folder);

		if (!is_dir($local))
		{
			throw new \InvalidArgumentException(
				'Argument $folder must be an existing folder,'
					. " '{$folder}' is not ({$local})"
			);
		}

		Verbose::log("Scan: {$folder} (in {$this->source})", 2);

		$dir = new \DirectoryIterator( $local );
		foreach ($dir as $found)
		{
			if ($found->isDot())
			{
				continue;
			}

			$original = $this->remote( $found->getPathname() );

			if ($this->skipScan( $found->getFileInfo() ))
			{
				Verbose::log("Scan.ignore: {$original}", 2);
				continue;
			}

			if ($found->isDir())
			{
				$this->scan( $original );
				continue;
			}

			Verbose::log("Scan.found: {$original}", 1);

			$file = new File($original, $this->read( $original ));
			Find\Traverser::traverse($file, $this->storage);
		}
	}

	protected function skipScan(\SplFileInfo $file)
	{
		// folders first...
		//
		if ($file->isDir())
		{
			if ('.git' == $file->getBasename() )
			{
				return true;
			}

			if ('wp-content' == $file->getBasename() )
			{
				return true;
			}

			// temporary, skip wp-admin
			if ('wp-admin' == $file->getBasename() ) return true;

			return false;
		}

		// ...files second
		//
		if ('php' != $file->getExtension())
		{
			return true;
		}

		// temporary, skip class declarations
		if (false !== strpos($file->getBasename(), 'class') ) return true;

		return false;
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
					. " (in {$this->source})"
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
}
