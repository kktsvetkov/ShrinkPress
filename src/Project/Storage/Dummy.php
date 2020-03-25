<?php

namespace ShrinkPress\Build\Project\Storage;

use ShrinkPress\Build\Project\Entity;

class Dummy extends StorageAbstract
{
	function readFunction($name)
	{
		return new Entity\WpFunction($name);
	}

	function writeFunction(Entity\WpFunction $entity)
	{

	}
}
