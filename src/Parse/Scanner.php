<?php

namespace ShrinkPress\Build\Parse;

use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Entity;
use ShrinkPress\Build\Index;

class Scanner
{
	protected $source;

	protected $index;

	function __construct(Source $source, Index\Index_Abstract $index)
	{
		$this->source = $source;
		$this->index = $index;
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
		Assist\Verbose::log("Scan: {$folder} (in {$basedir})", 2);

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
				if ($this->skipFolder( $local ))
				{
					Assist\Verbose::log("Folder ignored: {$local}", 2);
				} else
				{
					$sub = $this->scanFolder($local);
					$result = array_merge($result, $sub);
				}

				continue;
			}

			if ($this->skipFile( $local ))
			{
				Assist\Verbose::log("File ignored: {$local}", 2);
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
		Assist\Verbose::log("Scan: {$filename}", 1);
		$entity_file = Entity\Files\WordPress_PHP::factory( $filename );

		$code = $this->source->read( $filename );
		Assist\Code::extractPackage($code, $entity_file);

		$this->index->writeFile($entity_file);

		$traverser = Traverser::instance();
		$traverser->traverse(
			$filename,
			$nodes = $traverser->parse( $code ),
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
