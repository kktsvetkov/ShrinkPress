<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

use ShrinkPress\Build\Storage;

use \ShrinkPress\Build\Entity;

abstract class VisitorAbstract extends NodeVisitorAbstract
{
	protected $filename;
	protected $storage;

	static protected $entity_files_register;
	protected $entity_file;

	function load( $filename, Storage\StorageAbstract $storage)
	{
		$this->filename = (string) $filename;
		$this->storage = $storage;

		if (empty(self::$entity_files_register))
		{
			self::$entity_files_register = Entity\Register\Files::instance();
		}

		self::$entity_files_register->addFile(
			$this->entity_file = new Entity\Files\PHP_File(
				$this->filename
				)
		);
	}

	protected $result = array();

	function beforeTraverse(array $nodes)
	{
		$this->result = array();
	}

	abstract function flush(array $result, Storage\StorageAbstract $storage);

	function afterTraverse(array $nodes)
	{
		if ($this->result)
		{
			$this->flush($this->result, $this->storage);
			$this->result = array();

			self::$entity_files_register->save();
		}
	}
}
