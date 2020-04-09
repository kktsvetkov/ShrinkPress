<?php

namespace ShrinkPress\Build\Unparse\Task;

use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Entity;
use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;

class CreateComposer extends TaskAbstract
{
	function build(Source $source, Storage\StorageAbstract $storage)
	{
		// start with composer.json ...
		//
		$composerJson = Entity\File\Composer_JSON::instance();
		$source->write('composer.json', json_encode(
			$composerJson->jsonSerialize(),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
			));

		// ... then dump the autoload
		//
		Assist\ComposerPhar::dumpautoload( $source->basedir() );
	}
}
