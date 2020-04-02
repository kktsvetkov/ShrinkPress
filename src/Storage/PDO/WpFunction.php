<?php

namespace ShrinkPress\Build\Storage\PDO;

use ShrinkPress\Build\Parse\Entity;
use ShrinkPress\Build\Storage;

class WpFunction
{
	static function exists( $functionName, \PDO $pdo )
	{
		$sql = 'SELECT * FROM pdo_shrinkpress_functions WHERE functionName = ? LIMIT 0, 1';
		$q = $pdo->prepare($sql);
		$q->execute([ (string) $functionName ]);

		if (!$q->rowCount())
		{
			return false;
		}

		return $q->fetch( $this->pdo::FETCH_ASSOC );
	}

	static function write( Entity\WpFunction $entity, \PDO $pdo )
	{
		$sql = ' `filename` = ?,
			`line` = ?,
			`end` = ?,
			`docCommentLine` = ?,
			`classNamespace` = ?,
			`className` = ?,
			`classMethod` = ?,
			`classFile` = ?
			';

		if (!$found = static::exists($entity->functionName, $pdo))
		{
			$sql = 'INSERT IGNORE INTO pdo_shrinkpress_functions SET '
				. $sql . ', `functionName` = ? ';
		} else
		{
			$sql = 'UPDATE pdo_shrinkpress_functions SET '
				. $sql . ' WHERE `functionName` = ? ';
		}

		$q = $pdo->prepare( $sql );
		$q->execute(array(
			$entity->filename,
			$entity->line,
			$entity->end,
			$entity->docCommentLine,
			$entity->classNamespace,
			$entity->className,
			$entity->classMethod,
			$entity->classFile,
			$entity->functionName,
		));
	}

	static function all(\PDO $pdo)
	{
		$q = $pdo->query(
			'SELECT functionName FROM pdo_shrinkpress_functions ORDER BY functionName'
			);
		return $q->fetchAll($this->pdo::FETCH_COLUMN, 0);
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS pdo_shrinkpress_functions; ')->execute();
		$pdo->prepare('CREATE TABLE pdo_shrinkpress_functions (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				functionName varchar(255) NOT NULL,
				filename varchar(255) NOT NULL DEFAULT "",
				`line` int(11) NOT NULL DEFAULT 0,
				`end` int(11) NOT NULL DEFAULT 0,
				docCommentLine int(11) NOT NULL DEFAULT 0,
				classNamespace varchar(255) NOT NULL DEFAULT "",
				className varchar(255) NOT NULL DEFAULT "",
				classMethod varchar(255) NOT NULL DEFAULT "",
				classFile varchar(255) NOT NULL DEFAULT "",
			PRIMARY KEY (id),
			UNIQUE KEY functionName (functionName),
			KEY classID (classNamespace, className)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
