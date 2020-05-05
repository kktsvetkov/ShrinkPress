<?php

namespace ShrinkPress\Evolve;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class FindFunction extends NodeVisitorAbstract
{
	public $result = array();

	function beforeTraverse(array $nodes)
	{
		$this->result = array();
	}

	function leaveNode(Node $node)
	{
		if (!$node instanceof Node\Stmt\Function_)
		{
			return;
		}

		$this->result[] = $node;
	}

	function extract(Node $node)
	{
		$f = array(
			'function' => (string) $node->name,
			'startLine' => $node->getStartLine(),
			'endLine' => $node->getEndLine(),
			'docCommentLine' => 0,
		);

		if ($docComment = $node->getDocComment())
		{
			$f['docCommentLine'] = $docComment->getLine();
		}

		return $f;
	}
}
