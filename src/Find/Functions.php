<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Storage;

class Functions extends Visitor
{
	function leaveNode(Node $node)
	{
		if (!$node instanceof Node\Stmt\Function_)
		{
			return;
		}

		$found = array(
			'name' => (string) $node->name,
			'startLine' => $node->getStartLine(),
			'endLine' => $node->getEndLine(),
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
				"Function: {$found['name']}() at "
				 	. $this->filename . ':'
					. $found['startLine'],
				1);

			$found['fileOrigin'] = $this->filename;
			$entity = $storage->readFunction( $found['name'] );
			$entity->load($found);
			$storage->writeFunction( $entity );
		}
	}
}
