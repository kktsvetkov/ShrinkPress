<?php

namespace ShrinkPress\Build\File;

use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Source;

class Register
{
	use Assist\Instance;

	protected $build;

	function setBuildSource(Source $build)
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
		$this->files[ $key ] = $file;

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

		if ($json = $this->build->read($saved))
		{
			$file->restore((array) json_decode($json, true));
			return true;
		}

		return false;
	}

	function save( $filename = '')
	{
		$filename = (string) $filename;
		if ($filename)
		{
			if (!empty($this->files[ $filename ]))
			{
				$this->build->write(
					$filename . '.shrink',
					json_encode( $this->files[ $filename ] )
					);
				return $this;
			}

			throw new \InvalidArgumentException(
				"File {$filename} not in register"
			);
		}

		foreach($this->files as $filename => $file)
		{
			$this->build->write(
				$filename . '.shrink',
				json_encode($file, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
				);
		}

		return $this;
	}

	function __destruct()
	{
		$this->save();
	}
}
