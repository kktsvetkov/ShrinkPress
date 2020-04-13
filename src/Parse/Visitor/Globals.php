<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;

use ShrinkPress\Build\Index;
use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Entity;

class Globals extends Visitor_Abstract
{
	const ignore = array(
		'HTTP_RAW_POST_DATA',
		'PHP_SELF',
		);

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
			$this->result[ (string) $global->name ][] = array(
				'globalType' => 'keyword',
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

		$this->result[ $globalName ][] = array(
			'globalType' => 'array',
			'startLine' => $node->dim->getStartLine(),
			);
	}

	function flush(array $result, Index\Index_Abstract $index)
	{
		$file = $index->readFile( $this->filename );

		foreach($result as $globalName => $mentions)
		{
			if (in_array($globalName, self::ignore))
			{
				continue;
			}

			$entity = $index->readGlobal( $globalName );
			foreach ($mentions as $found)
			{
				Assist\Verbose::log(
					"Global: \${$globalName} at "
					 	. $this->filename . ':'
						. $found['startLine'],
					1);

				$entity->addMention(
					$file,
					$found['startLine'],
					$found['globalType']
					);
			}

			$index->writeGlobal( $entity );
		}

		$index->writeFile( $file );
	}
}
