<?php

namespace ShrinkPress\Build\Project;

use PhpParser\Node;
use PhpParser\Error;
use PhpParser\ParserFactory;

use ShrinkPress\Build\Verbose;

class File
{
	protected $filename;

	function __construct($filename, $code)
	{
		$this->filename = $filename;
		$this->nodes = self::parse($code);
	}

	function filename()
	{
		return $this->filename;
	}

	protected $nodes = [];
	protected static $parser;

	protected static function parse($code)
	{
		if (empty(static::$parser))
		{
			static::$parser = (new ParserFactory)
				->create(ParserFactory::PREFER_PHP7);
		}

		return static::$parser->parse($code);
	}

	function parsed()
	{
		return $this->nodes;
	}

}
