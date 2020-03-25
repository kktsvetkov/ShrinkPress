<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Project\Storage;

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

		$found['isPrivate'] = false;
		if ($docComment = $node->getDocComment())
		{
			$found['docComment'] = (string) $docComment;
			$found['docCommentLine'] = $docComment->getLine();

			// is it private ?
			//
			$found['isPrivate'] = (false !== strpos(
				$found['docComment'],
				'@access private'
				));
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

			$func = $storage->readFunction( $found['name'] );
			$found['fileOrigin'] = $this->filename;
			$func->load($found);

			$storage->writeFunction( $func );
		}
	}
}
