<?php

namespace ShrinkPress\Build\Index\PDO;

use ShrinkPress\Build\Entity;
use ShrinkPress\Build\Index;

class Globals
{
	static function write( Entity\Globals\Global_Entity $entity, \PDO $pdo )
	{
		$sql = 'INSERT IGNORE INTO shrinkpress_globals
			(_class, globalName, filename, line, globalType)
			VALUES (?, ?, ?, ?, ?); ';

		$q = $pdo->prepare($sql);
		$data = $entity->jsonSerialize();

		if (!empty($entity->pdo_mentions_count))
		{
			$data['mentions'] = array_slice(
				$data['mentions'],
				$entity->pdo_mentions_count
				);
		}

		foreach ($data['mentions'] as $mention)
		{
			$q->execute([
				get_class($entity),
				$entity->globalName(),
				$mention[0],
				$mention[1],
				$mention[2],
			]);
		}
	}

	static function read( $globalName, \PDO $pdo )
	{
		$sql = 'SELECT * FROM shrinkpress_globals WHERE globalName = ? ';
		$q = $pdo->prepare($sql);
		$q->execute([ (string) $globalName ]);

		$mentions = $q->rowCount()
			? $q->fetchAll( $pdo::FETCH_ASSOC )
			: array();

		if (!empty($mentions[0]['_class']))
		{
			$entity = new $mentions[0]['_class']( $globalName );
		} else
		{
			$entity = new Entity\Globals\WordPress_Global( $globalName );
		}

		foreach ($mentions as $mention)
		{
			$file_entity = new Entity\Files\PHP_File( $mention['filename'] );
			$entity->addMention($file_entity, $mention['line'], $mention['globalType']);
		}
		$entity->pdo_mentions_count = count( $mentions );

		return $entity;
	}

	static function all(\PDO $pdo)
	{
		return Index\Index_PDO::all( $pdo, 'globalName', 'shrinkpress_globals' );
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS shrinkpress_globals; ')->execute();
		$pdo->prepare('CREATE TABLE shrinkpress_globals (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				globalName varchar(255) NOT NULL,
				filename varchar(255) NOT NULL DEFAULT "",
				`line` int(11) NOT NULL DEFAULT 0,
				globalType enum("array", "keyword") NOT NULL DEFAULT "keyword",
				_class varchar(255) NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY origin (globalName, filename, line),
			KEY globalName (globalName)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
