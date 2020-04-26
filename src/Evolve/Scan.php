<?php

namespace ShrinkPress\Reframe\Evolve;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

class Scan
{
	protected $parser;
	protected $traverser;

	protected $wordPressFolder = '';

	protected $findFunctions;
	protected $findCalls;

	function __construct($wordPressFolder)
	{
		$this->traverser = new NodeTraverser;
		$this->parser = (new ParserFactory)
			->create(ParserFactory::PREFER_PHP7);

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
			$changes = $this->scanFolder('');
// BREAK;
		} while ($changes);

		$this->removeIncludes();
		$this->deleteOldFiles();
		Git::dotGit();
	}

	function scanFolder($folder)
	{
		$full = $this->wordPressFolder . '/' . $folder;
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
				$this->wordPressFolder . '/',
				'',
				$found->getPathname()
				);
			if ($found->isDir())
			{
				if (!in_array( $local, static::skipFolders ))
				{
					if ($changes = $this->scanFolder($local))
					{
						return $changes;
					}
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

			if ($changes = $this->scanFile($local))
			{
				return $changes;
			}
		}

		return 0;
	}

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

	function scanFile($filename)
	{
		$code = file_get_contents( $filename );
		$nodes = $this->parser->parse($code);

		// global ?
		//
		;
		;

		// function ?
		//
		if ($f = $this->functionFound($code, $nodes))
		{
			$f['filename'] = $filename;
			if ($m = Migration::getFunction($f))
			{
				print_r($f);

				$f = Code::extractDefinition($code, $f);
				Move::moveFunction($f, $m);
				Replace::replaceFunction($f, $m);

				file_put_contents($filename, $code);
				Git::commit("drop {$f['function']}()");

				file_put_contents(
					'functions.csv',
					"{$f['function']}, {$m['full']}, {$filename}:{$f['startLine']}\n",
					FILE_APPEND
					);
				return 1;
			} else
			{
				echo "UNKNOWN ?\n";
				print_r($f);exit;
			}
		}

		// class ?
		//
		;
		;

	}

	function functionFound($code, $nodes)
	{
		$this->traverser->addVisitor( $this->findFunctions );
		$this->traverser->traverse( $nodes );
		$this->traverser->removeVisitor( $this->findFunctions );

		if (!$this->findFunctions->result)
		{
			return false;
		}

		// does it have other things that
		// are about to change inside it ?
		//
		$node = $this->findFunctions->result;
		if ($this->hasMoreInside($node))
		{
			return false;
		}

		return $this->findFunctions->extract($node);
	}

	function classFound($code, $nodes)
	{

	}

	function globalFound($code, $nodes)
	{

	}

	/**
	* Check if there are any references to other things that are about to change
	*/
	function hasMoreInside(Node $node)
	{
		// calls ?
		//
		$this->traverser->addVisitor( $this->findCalls );
		$this->traverser->traverse( [$node] );
		$this->traverser->removeVisitor( $this->findCalls );

		if ($this->findCalls->result)
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
