<?php

namespace ShrinkPress\Evolve;

use PhpParser\Node;

class ScanGlobals
{
	protected $parser;

	protected $hasGlobals;

	function __construct(Parse $parser)
	{
		$this->parser = $parser;

		$this->hasGlobals = new HasGlobalsStatements;
		$this->hasGlobals->pushNode = true;
		$this->hasGlobals->exitOnFirstMatch = false;

		foreach (HasInside::$has['globals'] as $global => $files)
		{
			$this->replaceGlobal($global, $files);
		}
	}

	function replaceGlobal($global, array $files)
	{
		echo "G {$global}, ", count($files), " files\n";

		$g = array(
			'global' => $global,

			// hack to group all statements
			// point to the same class
			//
			'filename' => "wordpress/{$global}.php",
		);

		if (!$m = Reframe::getGlobal($g))
		{
			echo "MISSING REFRAME ", json_encode($g), "\n";
			return false;
		}

		print_r($g);
		print_r($m);

		Move::moveGlobalStatement($g, $m);
		Git::commit("\${$g['global']} moved to {$m['full']}");

		foreach ($files as $filename => $occurences)
		{
			$code = file_get_contents( $filename );

			$nodes = $this->parser->parse($code);
			$nodes = $this->parser->traverse($this->hasGlobals, $nodes);
			if (!$nodes)
			{
				echo "(0) {$filename}\n";
				return false;
			}

			foreach ($nodes as $node)
			{
				$g = $this->extractStatement($node);
				if ($global != $g['global'])
				{
					continue;
				}

				$g['filename'] = $filename;

				$code = $this->replaceGlobalStatement($g, $m, $code);

				CsvLog::append('globals.csv', array(
					$global,
					$m['full'],
					$filename . ':' . $node->getStartLine()
				));
			}
		}

		Git::commit("{$g['global']}() replaced with {$m['full']}()");
	}

	function extractStatement(Node $node)
	{
		$f = array(
			'global' => (string) $node->name,
			'startLine' => $node->getStartLine(),
			'endLine' => $node->getEndLine(),
			'docCommentLine' => 0,
		);

		if ($docComment = $node->getDocComment())
		{
			$f['docCommentLine'] = $docComment->getLine();
		}

		return $f;
	}

	function replaceGlobalStatement(array $g, array $m, $code)
	{
		$lines = explode("\n", $code);

		$line = $lines[ $g['startLine']-1 ];
		$tokens = token_get_all('<?php ' . trim($line));
		array_shift($tokens);

		$seek = '$' . $g['global'];
		$modified = array();
		foreach ($tokens as $token)
		{
			if (!empty($token[0]))
			{
				if (382 == $token[0])
				{
					continue;
				}

				if (320 == $token[0])
				{
					if ($seek == $token[1])
					{
						continue;
					}
				}
			}

			$oken = is_scalar($token) ? $token : $token[1];
			$modified[] = $oken;
		}

		if (array('global', ';') == $modified)
		{
			$modified = array();
		}

		$modified[] = "{$seek} = {$m['class']}::\${$m['global']};\n";
		$lines[ $g['startLine']-1 ] = join(' ', $modified);

		$code = join("\n", $lines);
		$code = Code::addUse($code, $m['namespace'], $m['class']);

		return $code;
	}
}
