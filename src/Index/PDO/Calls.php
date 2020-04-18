<?php

namespace ShrinkPress\Build\Index\PDO;

use ShrinkPress\Build\Entity;
use ShrinkPress\Build\Index;

class Calls
{
	static function write( Entity\Funcs\Function_Entity $entity, \PDO $pdo )
	{
		$sql = 'INSERT IGNORE INTO shrinkpress_calls
			(functionName, filename, line) VALUES (?, ?, ?); ';

		$q = $pdo->prepare($sql);
		$data = $entity->jsonSerialize();

		if (!empty($entity->pdo_calls_count))
		{
			$data['calls'] = array_slice(
				$data['calls'],
				$entity->pdo_calls_count
				);
		}

		foreach ($data['calls'] as $call)
		{
			$q->execute([
				$entity->functionName(),
				$call[0],
				$call[1],
			]);
		}
	}

	static function read( Entity\Funcs\Function_Entity $entity, \PDO $pdo )
	{
		$sql = 'SELECT * FROM shrinkpress_calls WHERE functionName = ? ';

		$q = $pdo->prepare( $sql );
		$q->execute([ (string) $entity->functionName() ]);

		$calls = $q->rowCount()
			? $q->fetchAll( $pdo::FETCH_ASSOC )
			: array();
		foreach ($calls as $call)
		{
			$entity->addCall($call['filename'], $call['line']);
		}
		$entity->pdo_calls_count = count($calls);

		return $entity;
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS shrinkpress_calls; ')->execute();
		$pdo->prepare('CREATE TABLE shrinkpress_calls (
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
