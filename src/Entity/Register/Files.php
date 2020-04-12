<?php

namespace ShrinkPress\Build\Entity\Register;

use ShrinkPress\Build\Entity\Files\File_Entity;
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

	function addFile(File_Entity $file)
	{
		Packages::instance()->addPackage($file)->save();
		return $this->addEntity($file->filename(), $file);
	}

	function getFile($filename)
	{
		return $this->getEntity( $filename );
	}
}
