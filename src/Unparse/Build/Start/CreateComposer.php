<?php

namespace ShrinkPress\Build\Unparse\Build\Start;

use ShrinkPress\Build\Unparse;
use ShrinkPress\Build\Index;

use ShrinkPress\Build\Entity;
use ShrinkPress\Build\Assist;

class CreateComposer implements Unparse\Build\Task
{
	function build(Unparse\Source $source, Index\Index_Abstract $index )
	{
		// start with composer.json ...
		//
		$composerJson = Entity\Files\Composer_JSON::instance();
		$source->write('composer.json', json_encode(
			$composerJson->jsonSerialize(),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
			));

		// ... then dump the autoload
		//
		Assist\ComposerPhar::dumpautoload( $source->basedir() );
	}
}
