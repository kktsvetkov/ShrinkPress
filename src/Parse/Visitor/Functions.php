<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Storage;

class Functions extends VisitorAbstract
{
	function leaveNode(Node $node)
	{
		if (!$node instanceof Node\Stmt\Function_)
		{
			return;
		}

		$found = array(
			'functionName' => (string) $node->name,
			'startLine' => $node->getStartLine(),
			'endLine' => $node->getEndLine(),
			'docCommentLine' => 0,
		);

		if ($docComment = $node->getDocComment())
		{
			$found['docCommentLine'] = $docComment->getLine();
		}

		$this->result[] = $found;
	}

	function flush(array $result, Storage\StorageAbstract $storage)
	{
		foreach($result as $found)
		{
			Verbose::log(
				"Function: {$found['functionName']}() at "
				 	. $this->filename . ':'
					. $found['startLine'],
				1);

			$entity = $storage->readFunction( $found['functionName'] );

			$entity->filename = $this->filename;
			$entity->line = $found['startLine'];
			$entity->end = $found['endLine'];

			$entity->functionName = $found['functionName'];
			$entity->docCommentLine = $found['docCommentLine'];

			$storage->writeFunction( $entity );
		}
	}
}
