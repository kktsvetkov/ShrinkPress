<?php

namespace ShrinkPress\Build\Project\Storage;

abstract class StorageAbstract
{
	const ENTITY_CLASS = 'class';
	const ENTITY_FUNCTION = 'function';
	const ENTITY_GLOBAL = 'global';
	const ENTITY_INCLUDE = 'include';

	abstract function read($entity, $name);

	abstract function write($entity, $name, array $data);
}
