<?php

namespace ShrinkPress\Reframe\Evolve;

class Git
{
	static function checkout()
	{
		shell_exec('git checkout -- .');		
	}

	static function commit($msg)
	{
		echo "Git: {$msg}\n";
	}
}
