<?php

namespace ShrinkPress\Build\Entity\Register;

use ShrinkPress\Build\Entity\File;
use ShrinkPress\Build\Assist;

class Files extends Register_Abstract
{
	use Assist\Instance;

	function getFiles()
	{
		return $this->getEntities();
	}

	function getFilenames()
	{
		return $this->getKeys();
	}

	function addFile(File\File_Entity $file)
	{
		return $this->addEntity($file->filename(), $file);
	}

	function getFile($filename)
	{
		return $this->getEntity( $filename );
	}

	protected function findEntity($key, array $data)
	{
		// what type should we resurrect the entity ? composer, php, what ?
		//
		return false;
	}
}
