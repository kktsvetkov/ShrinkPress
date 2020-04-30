<?php

namespace ShrinkPress\Evolve;

abstract class Inspect
{
	protected $skipFolders = array();
	protected $skipFiles = array();

	function __construct(array $skipFolders, array $skipFiles)
	{
		$this->skipFolders = $skipFolders;
		$this->skipFiles = $skipFiles;
	}

	function inspectFolder($folder)
	{
		$full = getcwd() . '/' . $folder;
		if (!is_dir($full))
		{
			throw new \InvalidArgumentException(
				'Argument $folder must be an existing folder,'
					. " '{$folder}' is not ({$full})"
			);
		}

		$dir = new \DirectoryIterator( $full );
		foreach ($dir as $found)
		{
			if ($found->isDot())
			{
				continue;
			}

			$local = str_replace( getcwd() . '/', '', $found->getPathname() );

			if ($found->isDir())
			{
				if (!in_array( $local, $this->skipFolders ))
				{
					if (self::INSPECT_STOP == $this->inspectFolder($local))
					{
						return self::INSPECT_STOP;
					}
				}
				continue;
			}

			if (in_array( $local, $this->skipFiles ))
			{
				continue;
			}

			if ('php' != \pathinfo($local, PATHINFO_EXTENSION))
			{
				continue;
			}

			if (self::INSPECT_STOP == $this->inspectFile($local))
			{
				return self::INSPECT_STOP;
			}
		}

		return self::INSPECT_OK;
	}

	const INSPECT_OK = 0;
	const INSPECT_STOP = 2;

	abstract function inspectFile($filename);
}
