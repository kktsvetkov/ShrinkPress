<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Storage;

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
			$ns = !empty($found['namespace'])
				? "{$found['namespace']}//"
				: '';
			Verbose::log(
				"Class: {$ns}{$found['className']} at "
				 	. $this->filename . ':'
					. $found['startLine'],
				1);

			$this->wp_file->addClass($found);

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
