<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Assist\Internal;
use ShrinkPress\Build\Parse\Entity\WpCallback;

class Hooks extends VisitorAbstract
{
	const callback_functions = array(
		'add_filter' => 1,
		'has_filter' => 1,
		'remove_filter' => 1,

		'add_action' => 1,
		'has_action' => 1,
		'remove_action' => 1,
	);

	function leaveNode(Node $node)
	{
		if (!$node instanceof Node\Expr\FuncCall)
		{
			return;
		}

		if (!$node->name instanceOf Node\Name)
		{
			return;
		}

		$caller = $node->name->__toString();

		if (empty(self::callback_functions[ $caller ]))
		{
			return;
		}

		$arg_pos = self::callback_functions[ $caller ];
		if (empty($node->args[ $arg_pos ]))
		{
			return;
		}

		if (!$node->args[ $arg_pos ]->value instanceof Node\Scalar\String_)
		{
			return;
		}

		$callback = $node->args[ $arg_pos ]->value->value;
		if (Internal::isInternal( $callback ))
		{
			return;
		}

		$this->result[] = array(
			'hookName' => !empty($node->args[ 0 ]->value->value)
				? $node->args[ 0 ]->value->value
				: json_encode( $node->args[ 0 ] ),
			'functionName' => $callback,
			'line' => $node->getLine(),
			'hookFunction' => $caller,
			);
	}

	function flush(array $result, Storage\StorageAbstract $storage)
	{
		foreach($result as $found)
		{
			Verbose::log(
				"Callback: {$found['functionName']}() at "
				 	. $this->filename . ':'
					. $found['line'],
				1);

			$entity = new WpCallback( $found['functionName'] );

			$entity->filename = $this->filename;
			$entity->line = $found['line'];

			$entity->hookName = $found['hookName'];
			$entity->hookFunction = $found['hookFunction'];

			$storage->writeCallback( $entity );
		}
	}
}
