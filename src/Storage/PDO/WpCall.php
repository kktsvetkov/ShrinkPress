<?php

namespace ShrinkPress\Build\Storage\PDO;

use ShrinkPress\Build\Parse\Entity;
use ShrinkPress\Build\Storage;

class WpCall
{
	static function write( Entity\WpCall $entity, \PDO $pdo )
	{
		$sql = 'INSERT IGNORE INTO pdo_shrinkpress_functions_calls
			(functionName, filename, line) VALUES (?, ?, ?); ';

		$q = $pdo->prepare($sql);
		$q->execute([
			$entity->functionName,
			$entity->filename,
			$entity->line,
		]);
	}

	static function read( $functionName, \PDO $pdo )
	{
		$sql = 'SELECT * FROM pdo_shrinkpress_functions_calls WHERE functionName = ? ';

		$q = $pdo->prepare( $sql );
		$q->execute([ (string) $functionName ]);

		$calls = $q->fetchAll($this->pdo::FETCH_ASSOC);
		$result = array();

		foreach ($calls as $call)
		{
			$entity = new Entity\WpCall( $call['functionName'] );
			$entity->filename = $call['filename'];
			$entity->line = $call['line'];

			$result[] = $entity;
		}

		return $result;
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS pdo_shrinkpress_functions_calls; ')->execute();
		$pdo->prepare('CREATE TABLE pdo_shrinkpress_functions_calls (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				functionName varchar(255) NOT NULL,
				filename varchar(255) NOT NULL,
				line int(11) NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY origin (functionName, filename, line),
			KEY functionName (functionName)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
