<?php

namespace ShrinkPress\Build\Index\PDO;

use ShrinkPress\Build\Entity;
use ShrinkPress\Build\Index;

class Includes
{
	static function write( Entity\Includes\Include_Entity $entity, \PDO $pdo )
	{
		$sql = 'INSERT IGNORE INTO shrinkpress_includes
			(_class, includedFile, filename, line, docCommentLine, includeType)
			VALUES (?, ?, ?, ?, ?, ?); ';

		$q = $pdo->prepare($sql);
		$data = $entity->jsonSerialize();

		if (!empty($entity->pdo_includes_count))
		{
			$data['includes'] = array_slice(
				$data['includes'],
				$entity->pdo_includes_count
				);
		}

		foreach ($data['includes'] as $include)
		{
			$q->execute([
				get_class($entity),
				$entity->includedFile(),
				$include[0],
				$include[1],
				$include[2],
				$include[3],
			]);
		}
	}

	static function read( $includedFile, \PDO $pdo )
	{
		$sql = 'SELECT * FROM shrinkpress_includes WHERE includedFile = ? ';
		$q = $pdo->prepare($sql);
		$q->execute([ (string) $includedFile ]);

		$includes = $q->rowCount()
			? $q->fetchAll( $pdo::FETCH_ASSOC )
			: array();

		if (!empty($includes[0]['_class']))
		{
			$entity = new $includes[0]['_class']( $includedFile );
		} else
		{
			$entity = new Entity\Includes\WordPress_Include( $includedFile );
		}

		foreach ($includes as $include)
		{
			$file_entity = new Entity\Files\PHP_File( $include['filename'] );
			$entity->addInclude($file_entity,
				$include['line'],
				$include['docCommentLine'],
				$include['includeType']
				);
		}
		$entity->pdo_includes_count = count( $includes );

		return $entity;
	}

	static function all(\PDO $pdo)
	{
		return Index\Index_PDO::all( $pdo, 'includedFile', 'shrinkpress_includes' );
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS shrinkpress_includes; ')->execute();
		$pdo->prepare('CREATE TABLE shrinkpress_includes (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				includedFile varchar(255) NOT NULL,
				filename varchar(255) NOT NULL DEFAULT "",
				`line` int(11) NOT NULL DEFAULT 0,
				docCommentLine int(11) NOT NULL DEFAULT 0,
				includeType enum(
					"include",
					"include_once",
					"require",
					"require_once") NOT NULL DEFAULT "require_once",
				_class varchar(255) NOT NULL DEFAULT "",
			PRIMARY KEY (id),
			UNIQUE KEY origin (includedFile, filename, line),
			KEY includedFile (includedFile)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
