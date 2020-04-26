<?php

namespace ShrinkPress\Reframe\Evolve;

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

		$this->result = $node;

		// return NodeTraverser::STOP_TRAVERSAL;
		return 2;
	}

	function extract(Node $node)
	{
		$f = array(
			'function' => (string) $node->name,
			'startLine' => $node->getStartLine(),
			'endLine' => $node->getEndLine(),
			'docComment' => '',
			'docCommentLine' => 0,
		);

		if ($docComment = $node->getDocComment())
		{
			$f['docComment'] = $docComment->getText();
			$f['docCommentLine'] = $docComment->getLine();
		}

		return $f;
	}
}
