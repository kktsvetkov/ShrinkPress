<?php

namespace ShrinkPress\Reframe\Evolve;

class Replace
{
	static function replaceFunction(array $f, array $m)
	{

		Git::commit("{$f['function']}() replaced with {$m['full']}()");
	}
}
