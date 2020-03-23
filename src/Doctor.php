<?php

namespace ShrinkPress\Build;

use PhpParser\Node;
use PhpParser\Error;
use PhpParser\ParserFactory;

use ShrinkPress\Build\Disintegrate\Composer;
use ShrinkPress\Build\Disintegrate\Packages;

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

		$project = $this->project;
		$project->clear(Project::LOG_CHANGES);

		// start with composer.json ...
		//
		$this->composer = new Composer;
		$this->write('composer.json', $this->composer->json() );

		// ... then do the dot files
		//
		if ($gitignore = join("\n", self::gitignore))
		{
			$this->write('.gitignore', $gitignore );
		}

		if ($gitattributes = join("\n", self::gitattributes))
		{
			$this->write('.gitattributes', $gitattributes );
		}

		$packages = Packages::packages();
		foreach ($packages as $package)
		{
			$definition = Packages::definition($package);

			$this->wpFunctions($package, $definition);
			$this->wpClasses($package, $definition);
			$this->wpGlobals($package, $definition);
			$this->wpIncludes($package, $definition);
			$this->wpClean($package, $definition);
		}
	}

	const gitignore = array(
		'/composer.lock',
		);

	const gitattributes = array();

	protected function code($file)
	{
		$local = $this->folder . $file;
		return file_get_contents($local);
	}

	protected function write($file, $contents)
	{
		Verbose::log("Write: {$file}", 1);
		$this->project->log(Project::LOG_CHANGES, "Writing {$file}");

		$local = $this->folder . $file;
		$dir = dirname($local);
		if (!file_exists($dir))
		{
			mkdir($dir, 0777, true);
		}
		return file_put_contents($local, $contents);
	}

	protected function delete($file)
	{
		$local = $this->folder . $file;
		return unlink($local);
	}

	protected function wpFunctions($package, array $definition)
	{
		if (empty($definition['functions']))
		{
			return false;
		}

		$this->composer->add($definition['name']);

		foreach ($definition['functions'] as $def)
		{
			$old = $def[0];
			$new = !empty($def[1])
				? $def[1]
				: $def[0];

			$wpfunc = new WpFunction($old);
			$this->project->read( $wpfunc );
			;

		}
	}

	protected function wpClasses()
	{

	}

	protected function wpGlobals()
	{

	}

	protected function wpIncludes()
	{

	}

	protected function wpClean()
	{

	}
}
