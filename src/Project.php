<?php

namespace ShrinkPress\Evolve;

class Project
{
	protected $wordPressFolder = '';

	function __construct($wordPressFolder)
	{
		chdir($this->wordPressFolder = $wordPressFolder);
	}

	function run()
	{
		$methods = get_class_methods($this);
		foreach ($methods as $i => $method)
		{
			if (0 !== strpos($method, 'task_'))
			{
				unset($methods[$i]);
			}
		}

		sort($methods);
		foreach($methods as $i => $method)
		{
			echo (1 + $i), '. ', $method, "()\n";
			$this->$method();
		}
	}

	function task_0000_wipe()
	{
		// wipe the slate clean before starting
		//
		Git::checkout();
		Composer::wipeComposer();
		shell_exec('rm functions.csv');
	}

	function task_0100_load()
	{
		$this->parser = new Parse;
	}

	function task_0200_start()
	{
		// start with .gitignore and .gitattributes
		//
		Git::dotGit();

		// fresh copy of composer
		//
		Composer::plantComposer();
	}

	function task_0300_inside()
	{
		// learn what is inside
		//
		new HasInside($this->parser);
	}

	function task_0400_globals()
	{

	}

	function task_0500_classes()
	{

	}

	function task_0600_functions()
	{
		new ScanFunctions($this->parser);
	}

	function task_0700_remove_includes()
	{

	}

	function task_0800_delete_old_files()
	{
	}
}
