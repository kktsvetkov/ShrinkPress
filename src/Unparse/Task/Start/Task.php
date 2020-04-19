<?php

namespace ShrinkPress\Build\Unparse\Task\Start;

use ShrinkPress\Build\Unparse\Task\Group;
use ShrinkPress\Build\Unparse\Source;
use ShrinkPress\Build\Index;

class Task extends Group
{
	function __construct()
	{
		// restore original WordPress,
		// delete new files
		//
		$this->addTask( new Wipe );

		// write down .gitignore and .gitattributes
		//
		$this->addTask( new DotGit );

		// write down composer.json and do
		// the initial dumpautoload
		//
		$this->addTask( new CreateComposer );

		// insert the "vendor/autoload.php" include
		//
		$this->addTask( new PlantComposer );
	}
}
