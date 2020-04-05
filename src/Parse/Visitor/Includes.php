<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Parse\Entity\WpInclude;

class Includes extends VisitorAbstract
{
	const include_type = array(
		Node\Expr\Include_::TYPE_INCLUDE => 'include',
		Node\Expr\Include_::TYPE_INCLUDE_ONCE => 'include_once',
		Node\Expr\Include_::TYPE_REQUIRE => 'require',
		Node\Expr\Include_::TYPE_REQUIRE_ONCE => 'require_once',
	);

	protected $prettyPrinter;

	function __construct()
	{
		$this->prettyPrinter = new \PhpParser\PrettyPrinter\Standard;
		fclose(fopen(__FILE__ . '.txt', 'w'));
	}

	function leaveNode(Node $node)
	{
		if (!$node instanceof Node\Expr\Include_)
		{
			return;
		}

		if ($node->expr instanceof Node\Expr\Variable)
		{
			return;
		}

		if (!empty($node->expr->right) && $node->expr->right instanceOf Node\Scalar\Encapsed)
		{
			return;
		}

$node->expr->filename = $this->filename;
file_put_contents(__FILE__ . '.txt', print_r($node->expr, 1), FILE_APPEND);

		$includedFile = $this->prettyPrinter->prettyPrintFile([$node->expr]);

		if ($node->expr instanceOf Node\Scalar\String_)
		{
			$includedFile = (string) $node->expr->value;
		} else
		if (!empty($node->expr->right) && $node->expr->right instanceOf Node\Scalar\String_)
		{
			$includedFile = (string) $node->expr->right->value;
		}

		$found = array(
			'includedFile' => $includedFile,
			'includeType' => self::include_type[ $node->type ],
			'startLine' => $node->getStartLine(),
			'docCommentLine' => 0,
			'fromFolder' => '',
		);

		if ($docComment = $node->getDocComment())
		{
			$found['docCommentLine'] = $docComment->getLine();
		}

		if (!empty($node->expr->left))
		{
			$found['fromFolder'] = $this->from_folder($node->expr->left);
		}

		$this->result[] = $found;
	}

	protected function from_folder(Node $node)
	{
		if ($node instanceOf Node\Scalar\MagicConst\Dir)
		{
			$folder = dirname($this->filename);
			$fromFolder = ('.' != $folder) ? $folder : '';
		} else

		if ($node instanceOf Node\Expr\ConstFetch)
		{
			$fromFolder = (string) $node->name;
			switch ($fromFolder)
			{
				case 'ABSPATH':
					$fromFolder = '';
					break;

				case 'WPINC':
					$fromFolder = 'wp-includes';
					break;
			}
		} else
		{
			$fromFolder = $this->prettyPrinter->prettyPrintFile([$node]);

			if ("<?php\n\ndirname(__DIR__)" == $fromFolder)
			{
				$folder = dirname(dirname($this->filename));
				$fromFolder = ('.' != $folder) ? $folder : '';
			}

			if ("<?php\n\nABSPATH . WPINC")
			{
				$fromFolder = 'wp-includes';
			}
		}

// json_encode("");exit;

		return $fromFolder;
	}

	function flush(array $result, Storage\StorageAbstract $storage)
	{
		foreach($result as $found)
		{
			Verbose::log(
				"Included ({$found['includeType']}): {$found['includedFile']} at "
				 	. $this->filename . ':'
					. $found['startLine'],
				1);

			$entity = new WpInclude( $found['includedFile'] );

			$entity->filename = $this->filename;
			$entity->line = $found['startLine'];

			$entity->includedFile = ltrim($found['includedFile'], '/');
			$entity->fromFolder = trim($found['fromFolder'], '/');
			$entity->includeType = $found['includeType'];
			$entity->docCommentLine = $found['docCommentLine'];

			$storage->writeInclude( $entity );
		}
	}
}
