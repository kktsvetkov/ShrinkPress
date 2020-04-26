<?php

namespace ShrinkPress\Reframe\Evolve;

class Move
{
	static function moveFunction(array $f, array $m, $composer)
	{

		$composer->updateComposer();
		Git::commit("{$f['function']}() moved to {$m['full']}()");
	}
}
