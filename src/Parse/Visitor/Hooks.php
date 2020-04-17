<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Index;
use ShrinkPress\Build\Entity;

class Hooks extends Visitor_Abstract
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
		if (Assist\Internal::isInternal( $callback ))
		{
			return;
		}

		$this->result[ $callback ][] = array(
			'line' => $node->getLine(),
			'hookName' => !empty($node->args[ 0 ]->value->value)
				? $node->args[ 0 ]->value->value
				: json_encode( $node->args[ 0 ] ),
			'hookFunction' => $caller,
			);
	}

	function flush(array $result, Index\Index_Abstract $index)
	{
		foreach($result as $callback => $calls)
		{
			$entity = $index->readFunction( $callback );

			foreach ($calls as $found)
			{
				Assist\Verbose::log(
					"Callback: {$callback}() from '{$found['hookName']}' at "
					 	. $this->filename . ':'
						. $found['line'],
					1);

				$entity->addCallback(
					$this->filename,
					$found['line'],
					$found['hookName'],
					$found['hookFunction']
					);
			}

			$index->writeCallbacks($entity);
		}
	}
}
