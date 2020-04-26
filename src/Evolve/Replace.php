<?php

namespace ShrinkPress\Reframe\Evolve;

class Replace
{
	protected $inspect;
	protected $old;
	protected $new;

	function __construct($scan, $method, $old, $new)
	{
		$this->inspect = array($scan, $method);
		$this->old = $old;
		$this->new = $new;

		$this->scanFolder('');
	}

	function scanFolder($folder)
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

			$local = str_replace(
				getcwd() . '/',
				'',
				$found->getPathname()
				);

			if ($found->isDir())
			{
				if (!in_array( $local, static::skipFolders ))
				{
					$this->scanFolder($local);
				}
				continue;
			}

			if (in_array( $local, static::skipFiles ))
			{
				continue;
			}

			if ('php' != \pathinfo($local, PATHINFO_EXTENSION))
			{
				continue;
			}

			$scan = $this->inspect[0];
			$method = $this->inspect[1];
			$scan->$method($local, $this->old, $this->new);
		}
	}

	const skipFolders = array(
		'.git',
		'wp-content',
		'wp-admin/css',
		'wp-admin/images',
		'wp-admin/js',
		'wp-includes/js',
		Composer::vendors . '/composer/',
		);

	const skipFiles = array(
		'wp-config.php',
		'wp-config-sample.php',
		'wp-admin/includes/noop.php',
		Composer::vendors . '/autoload.php',
		);
}
