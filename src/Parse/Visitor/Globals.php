<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Parse\Entity\WpGlobal;

class Globals extends VisitorAbstract
{
	function leaveNode(Node $node)
	{
		if ($node instanceof Node\Expr\ArrayDimFetch)
		{
			if ($node->var instanceof Node\Expr\Variable)
			{
				if ('GLOBALS' == (string) $node->var->name)
				{
					$this->global_array_element($node);
				}
			}
		} else

		if ($node instanceof Node\Stmt\Global_)
		{
			$this->global_statement($node);
		}
	}

	function global_statement(Node\Stmt\Global_ $node)
	{
		foreach ($node->vars as $global)
		{
			$this->result[] = array(
				'globalName' => (string) $global->name,
				'globalType' => 'keyword',
				'filename' => $this->filename,
				'startLine' => $global->getStartLine(),
			);
		}
	}

	function global_array_element(Node\Expr\ArrayDimFetch $node)
	{
		$globalName = '';
		if ($node->dim instanceof Node\Expr\Variable)
		{
			$globalName = (string) $node->dim->name;
		} else
		if ($node->dim instanceof Node\Scalar\String_)
		{
			$globalName = (string) $node->dim->value;
		}

		if (!$globalName)
		{
			var_dump($node);exit;
		}

		$this->result[] = array(
			'globalName' => $globalName,
			'globalType' => 'array',
			'filename' => $this->filename,
			'startLine' => $node->dim->getStartLine(),
			);
	}

	function flush(array $result, Storage\StorageAbstract $storage)
	{
		foreach($result as $found)
		{
			Verbose::log(
				"Global: \${$found['globalName']} at "
				 	. $this->filename . ':'
					. $found['startLine'],
				1);

			$entity = new WpGlobal( $found['globalName'] );

			$entity->filename = $this->filename;
			$entity->line = $found['startLine'];
			$entity->globalType = $found['globalType'];

			$storage->writeGlobal( $entity );
		}
	}
}
