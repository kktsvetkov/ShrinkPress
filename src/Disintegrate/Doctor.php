<?php

namespace ShrinkPress\Build\Disintegrate;

use PhpParser\Node;
use PhpParser\Error;
use PhpParser\ParserFactory;

use ShrinkPress\Build\Project;
use ShrinkPress\Build\Verbose;

class Doctor
{
	protected $project;

	function __construct(Project $project)
	{
		$this->project = $project;
	}

	protected $folder;

	protected $composer;

	function shrink($folder)
	{
		if (!is_dir($folder))
		{
			throw new \InvalidArgumentException(
				"Argument \$folder must be an existing folder, '{$folder}' is not"
			);
		}

		$this->folder = rtrim($folder, '/') . '/';
		Verbose::log("WordPress: {$folder}", 1);

		$this->composer = new Composer;
		$this->write('composer.json', $this->composer->json() );
	}

	protected function code($file)
	{
		$local = $this->folder . $file;
		return file_get_contents($local);
	}

	protected function write($file, $contents)
	{
		Verbose::log("Write: {$file}", 1);

		$local = $this->folder . $file;
		return file_put_contents($local, $contents);
	}

	protected function delete($file)
	{
		$local = $this->folder . $file;
		return unlink($local);
	}
}
