<?php

namespace ShrinkPress\Build\Index\PDO;

use ShrinkPress\Build\Entity;
use ShrinkPress\Build\Index;

class Classes
{
	static function read( $className, \PDO $pdo )
	{
		$data = Index\Index_PDO::exists(
			$pdo,
			'className',
			$className,
			'shrinkpress_classes'
			);

		if (!empty($data['_class']))
		{
			$entity = new $data['_class']( $className );
		} else
		{
			$entity = new Entity\Classes\WordPress_Class( $className );
		}

		return $entity->load( $data );
	}

	static function write( Entity\Classes\Class_Entity $entity, \PDO $pdo )
	{
		$sql = ' _class = ?,
			`filename` = ?,
			`startLine` = ?,
			`endLine` = ?,
			`docCommentLine` = ?,
			`extends` = ?
			';

		$found = Index\Index_PDO::exists(
			$pdo,
			'className',
			$entity->className(),
			'shrinkpress_classes'
			);

		if (!$found)
		{
			$sql = 'INSERT IGNORE INTO shrinkpress_classes SET '
				. $sql . ', `className` = ? ';
		} else
		{
			$sql = 'UPDATE shrinkpress_classes SET '
				. $sql . ' WHERE `className` = ? ';
		}

		$q = $pdo->prepare( $sql );

		$data = $entity->jsonSerialize();
		$q->execute(array(
			get_class($entity),
			$data['filename'],
			$data['startLine'],
			$data['endLine'],
			$data['docCommentLine'],
			$data['extends'],
			$data['className'],
		));
	}

	static function all(\PDO $pdo)
	{
		return Index\Index_PDO::all( $pdo, 'className', 'shrinkpress_classes' );
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS shrinkpress_classes; ')->execute();
		$pdo->prepare('CREATE TABLE shrinkpress_classes (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				className varchar(255) NOT NULL,
				filename varchar(255) NOT NULL DEFAULT "",
				`startLine` int(11) NOT NULL DEFAULT 0,
				`endLine` int(11) NOT NULL DEFAULT 0,
				docCommentLine int(11) NOT NULL DEFAULT 0,
				extends varchar(255) NOT NULL DEFAULT "",
				_class varchar(255) NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY className (className)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
