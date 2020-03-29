<?php

namespace ShrinkPress\Build\Find;

use ShrinkPress\Build\Project\Storage;
use ShrinkPress\Build\Project\Source;
use ShrinkPress\Build\Verbose;

class Files
{
	protected $source;

	protected $storage;

	function __construct(Source $source, Storage\StorageAbstract $storage)
	{
		$this->source = $source;
		$this->storage = $storage;
	}

	/**
	* Scans the $folder for WordPress PHP files
	* @param string $folder a WP project folder, e.g. "wp-includes/"
	*/
	function scan($folder = '')
	{
		$this->storage->beforeScan();

		$this->scanFolder($folder, $this->source);

		$this->storage->afterScan();
	}

	protected function scanFolder($folder, Source $source)
	{
		$local = $source->local($folder);
		if (!is_dir($local))
		{
			throw new \InvalidArgumentException(
				'Argument $folder must be an existing folder,'
					. " '{$folder}' is not ({$local})"
			);
		}

		$basedir = $source->basedir();
		Verbose::log("Scan: {$folder} (in {$basedir})", 2);

		$dir = new \DirectoryIterator( $local );
		foreach ($dir as $found)
		{
			if ($found->isDot())
			{
				continue;
			}

			$original = $source->remote( $found->getPathname() );
			if ($this->skipScan( $found->getFileInfo() ))
			{
				Verbose::log("File ignored: {$original}", 2);
				continue;
			}

			if ($found->isDir())
			{
				$this->scanFolder($original, $source);
				continue;
			}

			$this->scanFile($original, $source);
		}
	}

	protected function scanFile($filename, Source $source)
	{
		Verbose::log("File found: {$filename}", 1);

		$traverser = Traverser::instance();

		$nodes = $traverser->parse( $source->read( $filename ) );
		$traverser->traverse( $filename, $nodes, $this->storage );
	}

	const skipFolders = array(
		'wp-content',

		'sodium_compat',
		'ID3',
		'SimplePie',

		'vendor',
		);

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

			if (in_array( $file->getBasename(), self::skipFolders ))
			{
				return true;
			}

			return false;
		}

		// ...files second
		//
		if ('php' != $file->getExtension())
		{
			return true;
		}

		return false;
	}
}
