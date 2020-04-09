<?php

namespace ShrinkPress\Build\Parse;

use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;
use ShrinkPress\Build\Verbose;

class Scanner
{
	protected $source;

	protected $storage;

	protected $traverser;

	function __construct(Source $source, Storage\StorageAbstract $storage)
	{
		$this->source = $source;
		$this->storage = $storage;
		$this->traverser = Traverser::instance();
	}

	/**
	* Scans the $folder for WordPress PHP files
	* @param string $folder a WP project folder, e.g. "wp-includes/"
	* @return array list of scanned files and folders
	*/
	function scanFolder($folder)
	{
		$source = $this->source;

		$full = $source->full($folder);
		if (!is_dir($full))
		{
			throw new \InvalidArgumentException(
				'Argument $folder must be an existing folder,'
					. " '{$folder}' is not ({$full})"
			);
		}

		$basedir = $source->basedir();
		Verbose::log("Scan: {$folder} (in {$basedir})", 2);

		$result = array();

		$dir = new \DirectoryIterator( $full );
		foreach ($dir as $found)
		{
			if ($found->isDot())
			{
				continue;
			}

			$local = $source->local( $found->getPathname() );
			if ($found->isDir())
			{
// CONTINUE;	
				if ($this->skipFolder( $local ))
				{
					Verbose::log("Folder ignored: {$local}", 2);
				} else
				{
					$sub = $this->scanFolder($local);
					$result = array_merge($result, $sub);
				}

				continue;
			}

			if ($this->skipFile( $local ))
			{
				Verbose::log("File ignored: {$local}", 2);
				continue;
			}

			$this->scanFile($result[] = $local);
		}

		return $result;
	}

	/**
	* Scans a WordPress source file
	* @param string $filename
	* @return array
	*/
	function scanFile($filename)
	{
		Verbose::log("Scan: {$filename}", 1);

		$traverser = $this->traverser;
		$traverser->traverse(
			$filename,
			$nodes = $traverser->parse( $this->source->read( $filename ) ),
			$this->storage
			);

		return $nodes;
	}

	/**
	* @see \ShrinkPress\Build\Parse\Scanner::skipFolder()
	*/
	const skipFolders = array(
		'.git',
		'wp-content',
		'wp-admin/css',
		'wp-admin/images',
		'wp-admin/js',
		'wp-includes/js',
		'wp-includes/vendor',
		);

	/**
	* Whether to ignore the folder when scanning
	* @param string $folder
	* @return boolean
	*/
	protected function skipFolder($folder)
	{
		$folder = (string) $folder;

		if (in_array( $folder, static::skipFolders ))
		{
			return true;
		}

		return false;
	}

	/**
	* @see \ShrinkPress\Build\Parse\Scanner::skipFile()
	*/
	const skipFiles = array(
		'wp-config.php',
		'wp-config-sample.php',
		);

	/**
	* Whether to ignore the file when scanning
	* @param string $filename
	* @return boolean
	*/
	protected function skipFile($filename)
	{
		$filename = (string) $filename;

		if (in_array( $filename, static::skipFiles ))
		{
			return true;
		}

		if ('php' != \pathinfo($filename, PATHINFO_EXTENSION))
		{
			return true;
		}

		return false;
	}
}
