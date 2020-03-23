<?php

namespace ShrinkPress\Build;

use PhpParser\Node;
use PhpParser\Error;
use PhpParser\ParserFactory;

class Scout
{
	protected $project;

	protected $parser;
	protected $findCalls;
	protected $findCallbacks;

	function __construct(Project $project)
	{
		$this->project = $project;
	}

	/**
	* Starts the project from the files in $base folder
	* @param $base $log
	*/
	protected function start($base)
	{
		Verbose::log("Source: {$base}", 1);
		$project = $this->project;

		$project->clear(Project::LOG_FOUND);
		$project->clear(Project::LOG_IGNORE);

		$project->clear(Project::LOG_FUNCTIONS);
		$project->clear(Project::LOG_CLASSES);
	}

	function scan($source)
	{
		if (!is_dir($source))
		{
			throw new \InvalidArgumentException(
				"Argument \$source must be an existing folder, '{$source}' is not"
			);
		}

		// $base is used to shorten the logged filenames
		//
		static $base;
		static $depth;
		if (empty($base))
		{
			$base = rtrim($source, '/') . '/';
			$depth = 0;

			$this->start($base);
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
				$this->project->log(Project::LOG_IGNORE,
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

			$this->project->log(Project::LOG_FOUND,
				$local = $this->base( $found->getPathname(), $base)
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

		$nodes = $this->parser->parse($code);
		foreach ($nodes as $node)
		{
			if ($node instanceof Node\Stmt\Function_)
			{
				$this->wp_function($node, $file);
			// } else
			// if ($node instanceof Node\Stmt\Class_)
			// {
			// 	$this->wp_class($node, $file);
			}
		}

		// find out what functions are referenced as callbacks
		//
		if (empty($this->findCallbacks))
		{
			$this->findCallbacks = new Find\Callbacks;
		}
		$callbacks = Find\Traverser::traverse($nodes, $this->findCallbacks);
		foreach ($callbacks as $cb)
		{
			Verbose::log("Callback: {$cb[0]}() at {$file}:{$cb[1]}", 3);

			$called = new WpFunction($cb[0]);
			$project->read($called);

			$called->callers[] = array(
				$file, $cb[1], $cb[2]
				);
			$project->write($called);
		}

		// find out all the function calls
		//
		if (empty($this->findCalls))
		{
			$this->findCalls = new Find\Calls;
		}
		$calls = Find\Traverser::traverse($nodes, $this->findCalls);
		foreach ($calls as $cb)
		{
			Verbose::log("Calls {$cb[0]}() at {$file}:{$cb[1]}", 2);

			$called = new WpFunction($cb[0]);
			$project->read($called);

			$called->callers[] = !empty($cb[2])
				? array( $file, $cb[1], $cb[2])
				: array( $file, $cb[1]);
			$this->project->write($called);
		}
	}

	protected function wp_function(Node $node, $file)
	{
		$this->project->log(Project::LOG_FUNCTIONS, (string) $node->name);
		Verbose::log(
			"Function: {$node->name}() at {$file}:" . $node->getStartLine(),
			1);

		$func = WpFunction::fromNode($node);
		$this->project->read($func);

		$func->file = $file;

		// tmp only
		$p = new \PhpParser\PrettyPrinter\Standard;
		$func->code = $p->prettyPrint([$node]);
		$func->guts = print_r($node, 1);
		// ^

		$this->project->write($func);
	}

	protected function wp_class(Node $node, $file)
	{

	}


}
