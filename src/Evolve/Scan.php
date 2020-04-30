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
		// Composer::vendors . '/composer',
		Composer::vendors,
		'wp-includes/sodium_compat',
		);

	const skipFiles = array(
		'wp-config.php',
		'wp-config-sample.php',
		'wp-admin/includes/noop.php',
		// Composer::vendors . '/autoload.php',
		);

	protected $wordPressFolder = '';

	protected $parser;

	protected $findFunctions;

	protected $hasCalls;
	protected $hasHooks;

	function __construct($wordPressFolder)
	{
		parent::__construct(self::skipFolders, self::skipFiles);

		$this->parser = new Parse;
		$this->findFunctions = new FindFunction;

		$this->hasCalls = new HasCalls;
		$this->hasCalls->exitOnFirstMatch = true;

		$this->hasHooks = new HasHooks;
		$this->hasHooks->exitOnFirstMatch = true;

		chdir($this->wordPressFolder = $wordPressFolder);

		// wipe the slate clean before starting
		//
		Git::checkout();
		Composer::wipeComposer();
		shell_exec('rm functions.csv');

		// start with .gitignore and .gitattributes
		//
		Git::dotGit();

		// fresh copy of composer
		//
		Composer::plantComposer();

		// learn what is inside
		//
		new HasInside($this->parser);

		$try = 0;
		do {
			echo "Try: ", ++$try, "\n";
			$changes = $this->inspectFolder('');
		} while ($changes);

		$this->removeIncludes();
		$this->deleteOldFiles();
	}

	function inspectFile($filename)
	{
		$code = file_get_contents( $filename );

		// global ?
		//
		;
		;


		// class ?
		//
		;
		;

		// function ?
		//
		if ($f = $this->functionFound($code))
		{
			$f['filename'] = $filename;
			// if ($m = Migration::getFunction($f))
			if ($m = Reframe::getFunction($f))
			{
				print_r($f);
				print_r($m);

				$f = Code::extractDefinition($code, $f);
				Move::moveFunction($f, $m);
				Git::commit("{$f['function']}() moved to {$m['full']}()");

				Replace::replaceFunction($this->parser, $f, $m, 'replaceCall');
				Replace::replaceFunction($this->parser, $f, $m, 'replaceHook');
				Git::commit("{$f['function']}() replaced with {$m['full']}()");

				// read the source code again, file might
				// have been changed in the mean time when
				// the hooks and calls were being converted
				//
				$code = file_get_contents( $filename );
				Code::extractDefinition($code, $f);
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
			echo "SKIP {$node->name}()\n";
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
		if ($this->parser->traverse($this->hasCalls, [$node]))
		{
			return true;
		}

		// hooks ?
		//
		if ($this->parser->traverse($this->hasHooks, [$node]))
		{
			return true;
		}

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
