<?php

namespace ShrinkPress\Build\Storage;

use ShrinkPress\Build\Project\Entity;

abstract class StorageAbstract
{
	abstract function clean();

	abstract function getFunctions();
	abstract function readFunction($name);
	abstract function writeFunction(Entity\WpFunction $entity);
}
