<?php

namespace ShrinkPress\Build\Storage;

use ShrinkPress\Build\Project\Entity;

abstract class StorageAbstract
{
	abstract function beforeScan();
	abstract function afterScan();

	abstract function getFunctions();
	abstract function readFunction($name);
	abstract function writeFunction(Entity\WpFunction $entity);
}
