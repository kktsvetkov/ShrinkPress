<?php

namespace ShrinkPress\Build\Storage\PDO;

use ShrinkPress\Build\Parse\Entity;
use ShrinkPress\Build\Storage;

class WpCallback
{
	static function write( Entity\WpCallback $entity, \PDO $pdo )
	{
		$sql = 'INSERT IGNORE INTO pdo_shrinkpress_functions_hooks
			(functionName, hookName, hookFunction, filename, line)
			VALUES (?, ?, ?, ?, ?); ';

		$q = $pdo->prepare($sql);
		$q->execute([
			$entity->functionName,
			$entity->hookName,
			$entity->hookFunction,
			$entity->filename,
			$entity->line,
		]);
	}

	static function read( $functionName, \PDO $pdo )
	{
		$sql = 'SELECT * FROM pdo_shrinkpress_functions_hooks WHERE functionName = ? ';

		$q = $pdo->prepare( $sql );
		$q->execute([ (string) $functionName ]);

		$calls = $q->fetchAll($pdo::FETCH_ASSOC);
		$result = array();

		foreach ($calls as $call)
		{
			$entity = new Entity\WpCallback( $call['functionName'] );
			$entity->filename = $call['filename'];
			$entity->line = $call['line'];

			$entity->hookName = $call['hookName'];
			$entity->hookFunction = $call['hookFunction'];

			$result[] = $entity;
		}

		return $result;
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS pdo_shrinkpress_functions_hooks; ')->execute();
		$pdo->prepare('CREATE TABLE pdo_shrinkpress_functions_hooks (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				functionName varchar(255) NOT NULL,
				hookName varchar(255) NOT NULL,
				hookFunction varchar(255) NOT NULL,
				filename varchar(255) NOT NULL,
				line int(11) NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY origin (functionName, filename, line),
			KEY functionName (functionName)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
