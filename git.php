<?php

class shrinkpress_git
{
	const source_repo = 'git@github.com:WordPress/WordPress';

	const source_folder = 'wordpress/';

	const reduced_repo = 'git@github.com:shrinkpress/reduced.git';

	const reduced_folder = 'reduced/';

	function __construct(array $argv)
	{
		array_shift($argv);
		$cmd = array_shift($argv);

		switch($cmd)
		{
			case 'source':
				$this->source( $argv );
				break;

			case 'reduced':
				$this->reduced( $argv );
				break;

			case 'export':
				$this->export( $argv );
				break;

			default:
				echo "[!] Unknown command\n";
				break;
		}
	}

	protected function shell($cmd)
	{
		echo "> {$cmd}\n";
		shell_exec($cmd);
	}

	protected function source(array $argv)
	{
		$folder = __DIR__ . '/' . self::source_folder;

		$cmd = 'git clone ' . self::source_repo . '.git '
			. escapeshellcmd($folder);
		$this->shell($cmd);

		$wp_config = __DIR__ . '/source/wp-config.php';
		if (file_exists($wp_config))
		{
			copy($wp_config, $folder . '/wp-config.php');
		}

		chdir($folder);
		$this->shell( 'git pull' );
	}

	protected function reduced(array $argv)
	{
		$folder = __DIR__ . '/' . self::reduced_folder;

		$cmd = 'git clone ' . self::reduced_repo . '.git '
			. escapeshellcmd($folder);
		$this->shell($cmd);

		chdir($folder);
		$this->shell( 'git pull' );
	}

	protected function export(array $argv)
	{
		$tag = array_shift($argv);
		if (!$tag)
		{
			die("[!] No tag\n");
		}

		$source = __DIR__ . '/' . self::source_folder;
		$reduced = __DIR__ . '/' . self::reduced_folder;

		chdir($source);
		$this->shell('git checkout ' . $tag);

		chdir($reduced);
		$this->shell('git branch from-' . $tag);
		$this->shell('git push --set-upstream origin from-' . $tag);
		$this->shell('git checkout from-' . $tag);

		$this->shell('rm -rf *.html *.txt *.php wp-admin/ wp-content/ wp-includes/');
		$this->shell('cp -R ' . $source . '* .');
	}
}

new shrinkpress_git($argv);
