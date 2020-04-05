<?php

namespace ShrinkPress\Build\Storage\PDO;

use ShrinkPress\Build\Parse\Entity;
use ShrinkPress\Build\Storage;

class WpInclude
{
	static function write( Entity\WpInclude $entity, \PDO $pdo )
	{
		$sql = 'INSERT IGNORE INTO pdo_shrinkpress_includes
			(includedFile, filename, line, includeType, fromFolder, docCommentLine)
			VALUES (?, ?, ?, ?, ?, ?); ';

		$q = $pdo->prepare($sql);
		$q->execute([
			$entity->includedFile,
			$entity->filename,
			$entity->line,
			$entity->includeType,
			$entity->fromFolder,
			$entity->docCommentLine,
		]);
	}

	static function read( $functionName, \PDO $pdo )
	{
		$sql = 'SELECT * FROM pdo_shrinkpress_includes WHERE functionName = ? ';

		$q = $pdo->prepare( $sql );
		$q->execute([ (string) $functionName ]);

		$calls = $q->fetchAll($pdo::FETCH_ASSOC);
		$result = array();

		foreach ($calls as $call)
		{
			$entity = new Entity\WpInclude( $call['includedFile'] );
			$entity->filename = $call['filename'];
			$entity->line = $call['line'];

			$entity->includeType = $call['includeType'];
			$entity->fromFolder = $call['fromFolder'];
			$entity->docCommentLine = $call['docCommentLine'];

			$result[] = $entity;
		}

		return $result;
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS pdo_shrinkpress_includes; ')->execute();
		$pdo->prepare('CREATE TABLE pdo_shrinkpress_includes (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				includedFile varchar(255) NOT NULL,
				fromFolder varchar(255) NOT NULL DEFAULT "",
				filename varchar(255) NOT NULL DEFAULT "",
				`line` int(11) NOT NULL DEFAULT 0,
				docCommentLine int(11) NOT NULL DEFAULT 0,
				includeType enum(
					"include",
					"include_once",
					"require",
					"require_once") NOT NULL DEFAULT "require_once",
			PRIMARY KEY (id),
			UNIQUE KEY origin (includedFile, filename, line),
			KEY includedFile (includedFile)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
