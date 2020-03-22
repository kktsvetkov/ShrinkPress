<?php

namespace ShrinkPress\Build;

use PhpParser\Node;
use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

class Scanner
{
	protected $project;

	protected $parser;

	function __construct($build)
	{
		$this->project = new Project($build);
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
				$project->log(Project::LOG_IGNORE,
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

			$project->log(Project::LOG_FOUND,
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
			$project->log(Project::LOG_ERROR, $file);
			$project->log(Project::LOG_ERROR, $e->__toString());
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

		// find out what functions are referenced as callbacks
		//
		if ($callbacks = FindCallbacks::getCallbacks($guts))
		{
			foreach ($callbacks as $cb)
			{
				Verbose::log("Callback: {$cb[0]}() at {$file}:{$cb[1]}", 3);

				$called = new WpFunction($cb[0]);
				$called->callers[] = array(
					$file, $cb[1], $cb[2]
					);
				$project->write($called);
			}
		}

		// find out all the function calls
		//
		if ($calls = FindCalls::getCalls( $guts ))
		{
			foreach ($calls as $cb)
			{
				Verbose::log("Calls {$cb[0]}() at {$file}:{$cb[1]}", 2);

				$called = new WpFunction($cb[0]);
				$called->callers[] = !empty($cb[2])
					? array( $file, $cb[1], $cb[2])
					: array( $file, $cb[1]);
				$this->project->write($called);

				if (!empty($cb[2]))
				{
					$caller = new WpFunction($cb[2]);
					$caller->calls[] = $cb[0];
					$this->project->write($caller);
				}
			}
		}
	}

	protected function wp_function(Node $node, $file)
	{
		$this->project->log(Project::LOG_FUNCTIONS, (string) $node->name);
		Verbose::log('Function: ' . (string) $node->name . '()', 1);

		$func = WpFunction::fromNode($node);
		$func->file = $file;
		Verbose::log("\tat {$file}:{$func->startLine}", 2);

		// tmp only
		// $p = new \PhpParser\PrettyPrinter\Standard;
		// $func->code = $p->prettyPrint([$node]);
		// $func->guts = print_r($node, 1);

		$this->project->write($func);
	}

	protected function wp_class(Node $node, $file)
	{

	}


}
