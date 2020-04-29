<?php

namespace ShrinkPress\Reframe\Evolve;

class HasInside extends Inspect
{
	protected $parser;

	protected $inspectors = array();
	static $has = array();

	const inside_json = 'inside.json';

	function __construct(Parse $parser)
	{
		parent::__construct(Scan::skipFolders, Scan::skipFiles);

		$this->parser = $parser;

		$this->inspectors = array(
			'calls' => new HasCalls,
			'hooks' => new HasHooks,
			'super_globals' => new HasSuperGlobals,
			'globals' => new HasGlobalsStatements,
			);

		if (file_exists(self::inside_json))
		{
			self::$has = json_decode(
				file_get_contents(self::inside_json),
				true
			);
		} else
		{
			$this->inspectFolder('');

			file_put_contents(
				self::inside_json,
				json_encode(self::$has,
					JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
				));
		}
	}

	function inspectFile($filename)
	{
		echo "> {$filename}\n";

		$code = file_get_contents( $filename );
		$nodes = $this->parser->parse($code);

		foreach ($this->inspectors as $type => $inspector)
		{
			if (!$found = $this->parser->traverse($inspector, $nodes))
			{
				continue;
			}

			foreach ($found as $match)
			{
				if (empty(self::$has[$type][$match][$filename]))
				{
					self::$has[$type][$match][$filename] = 1;
				} else
				{
					self::$has[$type][$match][$filename]++;
				}
			}
		}
	}
}
