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
		Git::checkout();

		$try = 0;
		do {
			echo "Try: ", ++$try, "\n";
			$changes = $this->scanFolder('');
// BREAK;
		} while ($changes);

		$this->removeIncludes();
		$this->deleteOldFiles();
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

			$local = str_replace($this->wordPressFolder . '/', '', $found->getPathname() );
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
		'wp-includes/vendor',
		);

	const skipFiles = array(
		'wp-config.php',
		'wp-config-sample.php',
		'wp-admin/includes/noop.php',
		);

	function scanFile($filename)
	{
		// echo $filename, "\n";

		$code = file_get_contents( $this->wordPressFolder . '/' . $filename );
		$nodes = $this->parser->parse($code);

		if ($f = $this->functionFound($code, $nodes))
		{
			$f['filename'] = $filename;
			if ($m = Migration::getFunction($f))
			{
				$c = Code::extract($code, $f['startLine'], $f['endLine']);
				$func = $c[0];
				$code = $c[1];

				if ($f['docCommentLine'])
				{
					$b = Code::extract($code, $f['docCommentLine'], $f['startLine']-1);
					$code = $b[1];
				}

				print_r($f);

				Move::moveFunction($m, $f);
				Git::commit("{$f['function']}() moved to {$m['full']}()");

				$replace = new Replace($this->wordPressFolder);
				$replace->replaceFunction($m, $f);
				Git::commit("{$f['function']}() replaced with {$m['full']}()");

				file_put_contents($this->wordPressFolder . '/' . $filename, $code);
				Git::commit("drop {$f['function']}()");

				return 1;
			} else
			{
				echo "UNKNOWN ?\n";
				print_r($f);exit;
			}
		}

		;
		;
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
		$this->traverser->addVisitor( $this->findCalls );
		$this->traverser->traverse( [$node] );
		$this->traverser->removeVisitor( $this->findCalls );

		if ($this->findCalls->result)
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

	function removeIncludes()
	{

	}

	function deleteOldFiles()
	{

	}
}
