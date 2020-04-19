<?php

namespace ShrinkPress\Build\Unparse\Build;

class Start extends Group
{
	function __construct()
	{
		// restore original WordPress,
		// delete new files
		//
		$this->addTask( new Start\Wipe );

		// write down .gitignore and .gitattributes
		//
		$this->addTask( new Start\DotGit );

		// write down composer.json and do
		// the initial dumpautoload
		//
		$this->addTask( new Start\CreateComposer );

		// insert the "vendor/autoload.php" include
		//
		$this->addTask( new Start\PlantComposer );
	}
}
