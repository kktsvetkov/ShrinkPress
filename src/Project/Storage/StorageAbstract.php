<?php

namespace ShrinkPress\Build\Project\Storage;

use ShrinkPress\Build\Project\Entity;

abstract class StorageAbstract
{
	abstract function readFunction($name);
	abstract function writeFunction(Entity\WpFunction $entity);
}
