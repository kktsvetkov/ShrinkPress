<?php

namespace ShrinkPress\Build\Storage\PDO;

use ShrinkPress\Build\Parse\Entity;
use ShrinkPress\Build\Storage;

class WpClass
{
	static function exists( $className, \PDO $pdo )
	{
		$sql = 'SELECT * FROM pdo_shrinkpress_classes WHERE className = ? LIMIT 0, 1';
		$q = $pdo->prepare($sql);
		$q->execute([ (string) $className ]);

		if (!$q->rowCount())
		{
			return false;
		}

		return $q->fetch( $pdo::FETCH_ASSOC );
	}

	static function write( Entity\WpClass $entity, \PDO $pdo )
	{
		$sql = ' `filename` = ?,
			`line` = ?,
			`end` = ?,
			`docCommentLine` = ?,
			`extends` = ?,
			`namespace` = ?
			';

		if (!$found = static::exists($entity->className, $pdo))
		{
			$sql = 'INSERT IGNORE INTO pdo_shrinkpress_classes SET '
				. $sql . ', `className` = ? ';
		} else
		{
			$sql = 'UPDATE pdo_shrinkpress_classes SET '
				. $sql . ' WHERE `className` = ? ';
		}

		$q = $pdo->prepare( $sql );
		$q->execute(array(
			$entity->filename,
			$entity->line,
			$entity->end,
			$entity->docCommentLine,
			$entity->extends,
			$entity->namespace,
			$entity->className,
		));
	}

	static function all(\PDO $pdo)
	{
		$q = $pdo->query(
			'SELECT className FROM pdo_shrinkpress_classes ORDER BY className'
			);
		return $q->fetchAll($pdo::FETCH_COLUMN, 0);
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS pdo_shrinkpress_classes; ')->execute();
		$pdo->prepare('CREATE TABLE pdo_shrinkpress_classes (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				className varchar(255) NOT NULL,
				namespace varchar(255) NOT NULL,
				filename varchar(255) NOT NULL DEFAULT "",
				`line` int(11) NOT NULL DEFAULT 0,
				`end` int(11) NOT NULL DEFAULT 0,
				docCommentLine int(11) NOT NULL DEFAULT 0,
				extends varchar(255) NOT NULL DEFAULT "",
			PRIMARY KEY (id),
			UNIQUE KEY fullClassName (namespace, className)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
