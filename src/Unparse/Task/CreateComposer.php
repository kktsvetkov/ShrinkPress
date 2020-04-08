<?php

namespace ShrinkPress\Build\Unparse\Task;

use ShrinkPress\Build\Assist;
use ShrinkPress\Build\File;
use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;

class CreateComposer extends TaskAbstract
{
	function build(Source $source, Storage\StorageAbstract $storage)
	{
		// start with composer.json ...
		//
		$composerJson = File\ComposerJson::instance();
		$source->write('composer.json', $composerJson->json() );

		// ... then dump the autoload
		//
		Assist\ComposerPhar::dumpautoload( $source->basedir() );
	}
}
