<?php

namespace ShrinkPress\Reframe\Evolve;

class ReplaceFunction extends Inspect
{
	protected $f;
	protected $m;

	protected $parser;

	function __construct(array $f, array $m, Parse $parser)
	{
		parent::__construct(Scan::skipFolders, Scan::skipFiles);

		$this->f = $f;
		$this->m = $m;

		$this->parser = $parser;

		$this->inspectFolder('');
	}

	function inspectFile($filename)
	{
		// echo "\t* {$filename}\n";
		$code = file_get_contents( $filename );
		$changes = 0;

		// calls ?
		//
		$nodes = $this->parser->parse($code);
		$replace = new ReplaceCalls($this->f['function']);
		if ($matches = $this->parser->traverse($replace, $nodes))
		{
			$changes += count($matches);
			$lines = explode("\n", $code);
			foreach ($matches as $line)
			{
				$lines[ $line-1 ] = Code::replaceCall(
					$lines[ $line-1 ],
					$this->f['function'],
					'\\' . $this->m['full']
				);
			}

			$code = join("\n", $lines);
		}

		// hooks ?
		//
		$nodes = $this->parser->parse($code);
		$replace = new ReplaceHooks($this->f['function']);
		if ($matches = $this->parser->traverse($replace, $nodes))
		{
			$changes += count($matches);
			$lines = explode("\n", $code);
			foreach ($matches as $line)
			{
				$lines[ $line-1 ] = Code::replaceHook(
					$lines[ $line-1 ],
					$this->f['function'],
					'\\' . $this->m['full']
				);
			}

			$code = join("\n", $lines);
		}

		if ($changes)
		{
			file_put_contents($filename, $code);
		}
	}
}
