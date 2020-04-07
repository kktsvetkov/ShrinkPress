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

		$includedFile = '';
		if ($node->expr instanceOf Node\Scalar\String_)
		{
			$includedFile = (string) $node->expr->value;
		} else
		if (!empty($node->expr->right) && $node->expr->right instanceOf Node\Scalar\String_)
		{
			$includedFile = (string) $node->expr->right->value;

			if ('.maintenance' == $includedFile)
			{
				return;
			}
		} else
		{
			return;
		}

		$includeFolder = '';
		$found = array(
			'includedFile' => $includedFile,
			'includeType' => self::include_type[ $node->type ],
			'startLine' => $node->getStartLine(),
			'docCommentLine' => 0,
		);

		if ($docComment = $node->getDocComment())
		{
			$found['docCommentLine'] = $docComment->getLine();
		}

		if (!empty($node->expr->left))
		{
			$includeFolder = $this->include_folder($node->expr->left);

			if ('WP_CONTENT_DIR' == $includeFolder)
			{
				return;
			}

			if ('WP_PLUGIN_DIR' == $includeFolder)
			{
				return;
			}
		}

		if ($includeFolder = trim($includeFolder, '/'))
		{
			$found['includedFile'] = $includeFolder
				. '/'
				. ltrim($found['includedFile'], '/');
		}

		$this->result[] = $found;
	}

	protected function include_folder(Node $node)
	{
		if ($node instanceOf Node\Scalar\MagicConst\Dir)
		{
			$folder = dirname($this->filename);
			$includeFolder = ('.' != $folder) ? $folder : '';
		} else

		if ($node instanceOf Node\Expr\ConstFetch)
		{
			$includeFolder = (string) $node->name;
			switch ($includeFolder)
			{
				case 'ABSPATH':
					$includeFolder = '';
					break;

				case 'WPINC':
					$includeFolder = 'wp-includes';
					break;
			}
		} else
		{
			$includeFolder = $this->prettyPrinter->prettyPrintFile([$node]);

			if ("<?php\n\ndirname(__DIR__)" == $includeFolder)
			{
				$folder = dirname(dirname($this->filename));
				$includeFolder = ('.' != $folder) ? $folder : '';
			}

			if ("<?php\n\nABSPATH . WPINC")
			{
				$includeFolder = 'wp-includes';
			}
		}

		return $includeFolder;
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

			$this->wp_file->addInclude($found);

			$entity = new WpInclude( $found['includedFile'] );

			$entity->filename = $this->filename;
			$entity->line = $found['startLine'];

			$entity->includedFile = ltrim($found['includedFile'], '/');
			$entity->includeType = $found['includeType'];
			$entity->docCommentLine = $found['docCommentLine'];

			$storage->writeInclude( $entity );
		}
	}
}
