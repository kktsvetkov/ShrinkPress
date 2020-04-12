<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Entity;

class Classes extends VisitorAbstract
{
	protected $namespace = '';

	function enterNode(Node $node)
	{
		if ($node instanceof Node\Stmt\Namespace_)
		{
			$this->namespace = (string) $node->name;
			return;
		}
	}

	function leaveNode(Node $node)
	{
		if ($node instanceof Node\Stmt\Namespace_)
		{
			$this->namespace = '';
			return;
		}

		if (!$node instanceof Node\Stmt\Class_)
		{
			return;
		}

		$found = array(
			'className' => (string) $node->name,
			'namespace' => $this->namespace,
			'extends' => '',
			'startLine' => $node->getStartLine(),
			'endLine' => $node->getEndLine(),
			'docCommentLine' => 0,
		);

		if ($docComment = $node->getDocComment())
		{
			$found['docCommentLine'] = $docComment->getLine();
		}

		if (!empty($node->extends))
		{
			$found['extends'] = (string) $node->extends;
		}

		$this->result[] = $found;
	}

	function flush(array $result, Storage\StorageAbstract $storage)
	{
		foreach($result as $found)
		{
			$found_class = (!empty($found['namespace'])
				? $found['namespace'] . '\\'
				: '') . $found['className'];
			Verbose::log(
				"Class: {$found_class} at "
				 	. $this->filename . ':'
					. $found['startLine'],
				1);

			// new class entity
			//
			$class_entity = new Entity\Classes\WordPress_Class( $found_class );
			$class_entity->load(array(
				'filename' => $this->filename,
				'extends' => $found['extends'],
				'startLine' => $found['startLine'],
				'endLine' => $found['endLine'],
				'docCommentLine' => $found['docCommentLine'],
			));
			$this->getFile()->addClass( $class_entity );

			// old class entity
			//
			$entity = $storage->readClass( $found['className'] );

			$entity->filename = $this->filename;
			$entity->line = $found['startLine'];
			$entity->end = $found['endLine'];

			$entity->className = $found['className'];
			$entity->namespace = $found['namespace'];
			$entity->extends = $found['extends'];
			$entity->docCommentLine = $found['docCommentLine'];

			$storage->writeClass( $entity );
		}
	}
}
