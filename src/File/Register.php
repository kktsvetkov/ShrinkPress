<?php

namespace ShrinkPress\Build\File;

use ShrinkPress\Build\Assist;

class Register
{
	use Assist\Instance;

	protected $build;

	function setBuildSource(Assist\Umbrella $build)
	{
		$this->build = $build;
	}

	protected $files = array();

	function getFiles()
	{
		return $this->files;
	}

	function getFilenames()
	{
		return array_keys($this->files);
	}

	function addFile(FileAbstract $file)
	{
		$key = $file->filename();
		if (empty($this->files[ $key ]))
		{
			$this->files[ $key ] = $file;

			$allfiles = FilesList::instance();
			$allfiles->addFile($key);
		}

		return $this;
	}

	function getFile($filename)
	{
		$filename = (string) $filename;

		if (!empty($this->files[ $filename ]))
		{
			return $this->files[ $filename ];
		}

		return null;
	}

	function restore($filename, FileAbstract $file)
	{
		$filename = (string) $filename;
		$saved = $filename . '.shrink';

		if ($this->build->exists($saved))
		{
			if ($json = $this->build->read($saved))
			{
				$file->restore((array) json_decode($json, true));
				$this->addFile( $file );
				return true;
			}
		}

		return false;
	}

	function save( $filename )
	{
		$filename = (string) $filename;

		if (empty($this->files[ $filename ]))
		{
			throw new \InvalidArgumentException(
				"File {$filename} not in register"
				);
		}

		$this->build->write(
			$filename . '.shrink',
			json_encode(
				$this->files[ $filename ],
				JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
			));

		return $this;
	}

	function __destruct()
	{
		foreach($this->files as $filename => $file)
		{
			$this->save($filename);
		}
	}
}
