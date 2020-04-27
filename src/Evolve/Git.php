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
		Linter::all();

		echo "Git: {$msg}\n";
	}

	static function dotGit()
	{
		// .gitignore
		//
		file_put_contents(
			'.gitignore',
			join("\n", array(
				'/composer.lock',
				'/wp-config.php',
				)
			));
		self::commit('Adding .gitignore');

		// .gitattributes
		//
		;
	}
}
