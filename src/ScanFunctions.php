<?php

namespace ShrinkPress\Evolve;

use PhpParser\Node;

class ScanFunctions extends Inspect
{
	protected $parser;

	protected $findFunctions;

	protected $hasCalls;
	protected $hasHooks;

	function __construct(Parse $parser)
	{
		parent::__construct(Inspect::skipFolders, Inspect::skipFiles);
		$this->parser = $parser;

		$this->findFunctions = new FindFunction;

		$this->hasCalls = new HasCalls;
		$this->hasCalls->exitOnFirstMatch = true;

		$this->hasHooks = new HasHooks;
		$this->hasHooks->exitOnFirstMatch = true;

		$try = 0;
		do {
			echo "Try: ", ++$try, "\n";
			$changes = $this->inspectFolder('');
		} while ($changes);
	}

	protected $empty = array();

	function inspectFile($filename)
	{
		// we looked at this file before,
		// there are no functions inside it
		//
		if (!empty($this->empty[ $filename ]))
		{
			return false;
		}

		$code = file_get_contents( $filename );

		$nodes = $this->parser->parse($code);
		$nodes = $this->parser->traverse($this->findFunctions, $nodes);
		if (!$nodes)
		{
			echo "(0) {$filename}\n";
			$this->empty[ $filename ] = 1;
			return false;
		}

		echo '(', count($nodes), ") {$filename}\n";

		$changed = 0;
		foreach ($nodes as $node)
		{
			// does it have other things that
			// are about to change inside it ?
			//
			if ($this->hasMoreInside($node))
			{
				echo "SKIP {$node->name}()\n";
				continue;
			}

			$f = $this->findFunctions->extract($node);
			$f['filename'] = $filename;

			if (!$m = Reframe::getFunction($f))
			{
				echo "MISSING REFRAME ", json_encode($f), "\n";
				continue;
			}

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

			$changed = true;
			break;
		}

		return $changed
			? Inspect::INSPECT_STOP
			: false;
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

		return false;
	}

}
