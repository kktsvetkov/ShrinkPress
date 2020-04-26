<?php

namespace ShrinkPress\Reframe\Evolve;

class HasInside extends Inspect
{
	protected $parser;

	protected $hasCalls;
	protected $hasHooks;
	protected $hasGlobals;

	function __construct(Parse $parser)
	{
		parent::__construct(Scan::skipFolders, Scan::skipFiles);

		$this->parser = $parser;

		$this->hasCalls = new HasCalls;
		$this->hasHooks = new HasHooks;
		// $this->hasGlobals = new HasGlobals;

		if (file_exists('inside.json'))
		{
			$json = json_decode(file_get_contents('inside.json'), true);
			self::$calls = $json['calls'];
			self::$hooks = $json['hooks'];
			self::$globals = $json['globals'];
			unset($json);
		} else
		{
			$this->inspectFolder('');
		}

		file_put_contents('inside.json', json_encode([
			'calls' => self::$calls,
			'hooks' => self::$hooks,
			'globals' => self::$globals,
		], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}

	static $calls = array();

	static $hooks = array();

	static $globals = array();

	function inspectFile($filename)
	{
		echo "> {$filename}\n";

		$code = file_get_contents( $filename );
		$nodes = $this->parser->parse($code);

		if ($calls = $this->parser->traverse($this->hasCalls, $nodes))
		{
			foreach($calls as $call)
			{
				if (empty(self::$calls[$call][$filename]))
				{
					self::$calls[$call][$filename] = 1;
				} else
				{
					self::$calls[$call][$filename]++;
				}
			}
		}

		if ($hooks = $this->parser->traverse($this->hasHooks, $nodes))
		{
			foreach($hooks as $hook)
			{
				if (empty(self::$hooks[$hook][$filename]))
				{
					self::$hooks[$hook][$filename] = 1;
				} else
				{
					self::$hooks[$hook][$filename]++;
				}
			}
		}
	}
}
