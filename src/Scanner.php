<?php

namespace ShrinkPress\Build;

use PhpParser\Node;
use PhpParser\Error;
use PhpParser\ParserFactory;

class Scanner
{
	protected $project;
	protected $build;

	const LOG_FOUND = 'found.txt';
	const LOG_IGNORE = 'ignore.txt';
	const LOG_ERROR = 'error.txt';

	protected $parser;

	function __construct($build)
	{
		$this->project = new Project($build);
		$this->build = $build;
	}

	function scan($source)
	{
		if (!is_dir($source))
		{
			throw new \InvalidArgumentException(
				"Argument \$source must be an existing folder, '{$source}' is not"
			);
		}

		$project = $this->project;

		// $base is used to shorten the logged filenames
		//
		static $base;
		static $depth;
		if (empty($base))
		{
			$base = rtrim($source, '/') . '/';
			$depth = 0;

			$project->start($base);
		} else
		{
			$depth++;
			Verbose::log("Folder: {$source}", 2);
		}

		$dir = new \DirectoryIterator( $source );
		foreach ($dir as $found)
		{
			if ($found->isDot())
			{
				continue;
			}

			if ($this->skip( $found->getFileInfo() ))
			{
				$project->log(self::LOG_IGNORE,
					$ignore = $this->base($found->getPathname(), $base)
					);
				Verbose::log("Ignore: {$ignore}", 2);
				continue;
			}

			if ($found->isDir())
			{
				$this->scan( $found->getPathname() );
				continue;
			}

			$project->log(self::LOG_FOUND,
				$local = $this->base($found->getPathname(), $base)
				);

			Verbose::log($local, 1);
			$code = file_get_contents( $found->getPathname() );
			$this->analyze( $code, $local );
		}

		if (--$depth === 0)
		{
			unset($base);
		}
	}

	protected function skip(\SplFileInfo $file)
	{
		// folders first...
		//
		if ($file->isDir())
		{
			if ('wp-content' == $file->getBasename() )
			{
				return true;
			}

			// temporary
			if ('wp-admin' == $file->getBasename() ) return true;

			return false;
		}

		// ...files second
		//
		if ('php' != $file->getExtension())
		{
			return true;
		}

		// temporary
		if (false !== strpos($file->getBasename(), 'class') ) return true;

		return false;
	}

	protected function base($file, $base)
	{
		if (0 === strpos($file, $base))
		{
			$file = substr($file, strlen($base));
		}

		return $file;
	}

	function analyze($code, $file)
	{
		if (empty($this->parser))
		{
			$this->parser = (new ParserFactory)
				->create(ParserFactory::PREFER_PHP7);
		}

		$project = $this->project;

		try {
			$guts = $this->parser->parse($code);
		} catch (\Error $e)
		{
			$project->log(self::LOG_ERROR, $file);
			$project->log(self::LOG_ERROR, $e->__toString());
			return false;
		}

		foreach ($guts as $node)
		{
			if ($node instanceof Node\Stmt\Function_)
			{
				$this->wp_function($node, $file);
			// } else
			// if ($node instanceof Node\Stmt\Class_)
			// {
			// 	$this->wp_class($node, $file);
			} else
			{
				echo "\t", get_class($node), "\n";
			}
		}
	}

	protected function wp_function(Node $node, $file)
	{
		Verbose::log('Function: ' . (string) $node->name . '()', 1);

		$func = WpFunction::fromNode($node);
		$func->file = $file;

		Verbose::log("\tat {$file}:{$func->startLine}", 2);

		// find out what functions are calls
		//
		if ($calls = FindCalls::getCalls($node))
		{
			$func->calls = $calls;

			foreach ($calls as $f)
			{
				Verbose::log("\tcalls {$f[0]}() at {$file}:{$f[1]}", 3);

				$called = new WpFunction($f[0]);
				$called->callers[] = array(
					$file, $f[1], (string) $node->name
					);
				$this->project->write($called);
			}
		}

		// tmp only
		$p = new \PhpParser\PrettyPrinter\Standard;
		$func->code = $p->prettyPrint([$node]);
		$func->guts = print_r($node, 1);

		$this->project->write($func);
		exit;
	}

	protected function wp_class(Node $node, $file)
	{

	}


}
