<?php

namespace ShrinkPress\Build\Storage\PDO;

use ShrinkPress\Build\Parse\Entity;
use ShrinkPress\Build\Storage;

class WpGlobal
{
	static function write( Entity\WpGlobal $entity, \PDO $pdo )
	{
		$sql = 'INSERT IGNORE INTO pdo_shrinkpress_globals
			(globalName, filename, line, globalType)
			VALUES (?, ?, ?, ?); ';

		$q = $pdo->prepare($sql);
		$q->execute([
			$entity->globalName,
			$entity->filename,
			$entity->line,
			$entity->globalType,
		]);
	}

	static function read( $globalName, \PDO $pdo )
	{
		$sql = 'SELECT * FROM pdo_shrinkpress_globals WHERE globalName = ? ';

		$q = $pdo->prepare( $sql );
		$q->execute([ (string) $globalName ]);

		$calls = $q->fetchAll($pdo::FETCH_ASSOC);
		$result = array();

		foreach ($calls as $call)
		{
			$entity = new Entity\WpGlobal( $call['globalName'] );
			$entity->filename = $call['filename'];
			$entity->line = $call['line'];

			$entity->globalType = $call['globalType'];

			$result[] = $entity;
		}

		return $result;
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS pdo_shrinkpress_globals; ')->execute();
		$pdo->prepare('CREATE TABLE pdo_shrinkpress_globals (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				globalName varchar(255) NOT NULL,
				filename varchar(255) NOT NULL DEFAULT "",
				`line` int(11) NOT NULL DEFAULT 0,
				globalType enum("array", "keyword") NOT NULL DEFAULT "keyword",
			PRIMARY KEY (id),
			UNIQUE KEY origin (globalName, filename, line),
			KEY globalName (globalName)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
