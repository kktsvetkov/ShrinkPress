<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;

use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Index;

class Classes extends Visitor_Abstract
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

		$className = (string) $node->name;
		$found = array(
			'namespace' => $this->namespace,
			'extends' => '',
			'filename' => $this->filename,
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

		$this->result[ $className ] = $found;
	}

	function flush(array $result, Index\Index_Abstract $index)
	{
		foreach($result as $className => $found)
		{
			$fullClassName = (!empty($found['namespace'])
				? $found['namespace'] . '\\'
				: '') . $className;

			Assist\Verbose::log(
				"Class: {$fullClassName} at "
				 	. $this->filename . ':'
					. $found['startLine'],
				1);

			$entity = $index->getClass( $fullClassName )->load( $found );
			$index->writeClass( $entity );

			$file = $index->readFile( $this->filename )->addClass( $entity );
			$index->writeFile( $file );
		}
	}
}
