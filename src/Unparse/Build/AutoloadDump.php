<?php

namespace ShrinkPress\Reframe\Unparse\Build;

use ShrinkPress\Reframe\Unparse;
use ShrinkPress\Reframe\Index;

use ShrinkPress\Reframe\Entity;
use ShrinkPress\Reframe\Assist;

class AutoloadDump implements Task
{
	function build(Unparse\Source $source, Index\Index_Abstract $index )
	{
		return self::dumpautoload( $source );
	}

	static function dumpautoload(Unparse\Source $source)
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
