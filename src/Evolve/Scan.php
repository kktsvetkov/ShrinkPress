<?php

namespace ShrinkPress\Reframe\Evolve;

use PhpParser\Node;

class Scan extends Inspect
{
	const skipFolders = array(
		'.git',
		'wp-content',
		'wp-admin/css',
		'wp-admin/images',
		'wp-admin/js',
		'wp-includes/js',
		Composer::vendors,
		'wp-includes/sodium_compat',
		);

	const skipFiles = array(
		'wp-config.php',
		'wp-config-sample.php',
		'wp-admin/includes/noop.php',
		);

	protected $wordPressFolder = '';

	protected $parser;

	protected $findFunctions;
	protected $findCalls;

	function __construct($wordPressFolder)
	{
		parent::__construct(self::skipFolders, self::skipFiles);

		$this->parser = new Parse;
		$this->findFunctions = new FindFunction;
		$this->findCalls = new FindCalls;

		chdir($this->wordPressFolder = $wordPressFolder);

		// wipe the slate clean before starting
		//
		Git::checkout();
		Composer::wipeComposer();
		shell_exec('rm functions.csv');

		// fresh copy of composer
		//
		Composer::plantComposer();

		$try = 0;
		do {
			echo "Try: ", ++$try, "\n";
			$changes = $this->inspectFolder('');
		} while ($changes);

		$this->removeIncludes();
		$this->deleteOldFiles();
		Git::dotGit();
	}

	function inspectFile($filename)
	{
		$code = file_get_contents( $filename );

		// global ?
		//
		;
		;

		// function ?
		//
		if ($f = $this->functionFound($code))
		{
			$f['filename'] = $filename;
			if ($m = Migration::getFunction($f))
			{
				print_r($f);

				$f = Code::extractDefinition($code, $f);
				Move::moveFunction($f, $m);

				// $this->replaceFunction($f, $m);
				new ReplaceFunction($f, $m, $this->parser);
				Git::commit("{$f['function']}() replaced with {$m['full']}()");

				file_put_contents($filename, $code);
				Git::commit("drop {$f['function']}()");

				file_put_contents(
					'functions.csv',
					"{$f['function']}, {$m['full']}, {$filename}:{$f['startLine']}\n",
					FILE_APPEND
					);

				return Inspect::INSPECT_STOP;
			}
		}

		// class ?
		//
		;
		;

	}

	function functionFound($code)
	{
		$nodes = $this->parser->parse($code);
		$node = $this->parser->traverse($this->findFunctions, $nodes);
		if (!$node)
		{
			return false;
		}

		// does it have other things that
		// are about to change inside it ?
		//
		if ($this->hasMoreInside($node))
		{
			return false;
		}

		return $this->findFunctions->extract($node);
	}

	function classFound($code)
	{

	}

	function globalFound($code)
	{

	}

	/**
	* Check if there are any references to other things that are about to change
	*/
	function hasMoreInside(Node $node)
	{
		// calls ?
		//
		if ($this->parser->traverse($this->findCalls, [$node]))
		{
			return true;
		}

		// hooks ?
		//
		;
		;

		// globals ?
		//
		;
		;

		return false;
	}

	function removeIncludes()
	{

	}

	function deleteOldFiles()
	{

	}
}
