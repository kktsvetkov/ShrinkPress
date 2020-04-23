<?php

namespace ShrinkPress\Reframe\Unparse\Build\External;

use ShrinkPress\Reframe\Unparse;
use ShrinkPress\Reframe\Index;

use ShrinkPress\Reframe\Entity;

class PhpMailer implements Unparse\Build\Task
{
	function build(Unparse\Source $source, Index\Index_Abstract $index )
	{
		$this->same($source, $index);
		// $this->latest($source, $index);
	}

	/**
	* Just move bundled PHPMailer into a package
	*/
	protected function same(Unparse\Source $source, Index\Index_Abstract $index )
	{
		// $files = $index->readPackage('PHPMailer')->files();
		/*
		1. get package name
		2. add package to composer
		3. add class to package "AS IS" without changing its class name;
			must be as "classmap" option, or PSR4 with empty namespace

		4. find the file where the class is declared
		5. remove require\include references to that original file
		*/

		// $composer = Entity\Files\Composer_JSON::instance();
		//
		// $folder = $composer::vendors . '/shrinkpress/bundled/';
		// $composer->addPsr4('', $folder);
	}

	/**
	* Get the latest PHPMailer, ignore the bundled one
	*/
	protected function latest(Unparse\Source $source, Index\Index_Abstract $index )
	{

	}
}
