<?php

namespace ShrinkPress\Reframe\Index\PDO;

use ShrinkPress\Reframe\Entity;
use ShrinkPress\Reframe\Index;

class Callbacks
{
	static function write( Entity\Funcs\Function_Entity $entity, \PDO $pdo )
	{
		$sql = 'INSERT IGNORE INTO shrinkpress_callbacks
			(functionName, filename, line, hookName, hookFunction)
			VALUES (?, ?, ?, ?, ?); ';

		$q = $pdo->prepare($sql);
		$data = $entity->jsonSerialize();

		if (!empty($entity->pdo_callbacks_count))
		{
			$data['callbacks'] = array_slice(
				$data['callbacks'],
				$entity->pdo_callbacks_count
				);
		}

		foreach ($data['callbacks'] as $callback)
		{
			$q->execute([
				$entity->functionName(),
				$callback[0],
				$callback[1],
				$callback[2],
				$callback[3],
			]);
		}
	}

	static function read( Entity\Funcs\Function_Entity $entity, \PDO $pdo )
	{
		$sql = 'SELECT * FROM shrinkpress_callbacks WHERE functionName = ? ';

		$q = $pdo->prepare( $sql );
		$q->execute([ (string) $entity->functionName() ]);

		$callbacks = $q->rowCount()
			? $q->fetchAll( $pdo::FETCH_ASSOC )
			: array();
		foreach ($callbacks as $callback)
		{
			$entity->addCall(
				$callback['filename'],
				$callback['line'],
				$callback['hookName'],
				$callback['hookFunction']
				);
		}
		$entity->pdo_callbacks_count = count($callbacks);

		return $entity;
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS shrinkpress_callbacks; ')->execute();
		$pdo->prepare('CREATE TABLE shrinkpress_callbacks (
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
