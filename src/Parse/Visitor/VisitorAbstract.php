<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

use ShrinkPress\Build\Storage;

abstract class VisitorAbstract extends NodeVisitorAbstract
{
	protected $filename;
	protected $storage;

	protected $wp_file;

	function load( $filename, Storage\StorageAbstract $storage)
	{
		$this->filename = (string) $filename;
		$this->storage = $storage;

		$register = \ShrinkPress\Build\File\Register::instance();
		if (!$this->wp_file = $register->getFile($this->filename))
		{
			$this->wp_file = new \ShrinkPress\Build\File\WordPress($this->filename);
		}
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
		}

		$register = \ShrinkPress\Build\File\Register::instance();
		$register->addFile($this->wp_file);
		$register->save( $this->wp_file->filename() );
	}
}
