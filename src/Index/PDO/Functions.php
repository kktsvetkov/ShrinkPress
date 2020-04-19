<?php

namespace ShrinkPress\Reframe\Index\PDO;

use ShrinkPress\Reframe\Entity;
use ShrinkPress\Reframe\Index;

class Functions
{
	static function read( $functionName, \PDO $pdo )
	{
		$data = Index\Index_PDO::exists(
			$pdo,
			'functionName',
			$functionName,
			'shrinkpress_functions'
			);

		if (!empty($data['_class']))
		{
			$entity = new $data['_class']( $functionName );
		} else
		{
			$entity = new Entity\Funcs\WordPress_Func( $functionName );
		}

		return $entity->load( $data );
	}

	static function write( Entity\Funcs\Function_Entity $entity, \PDO $pdo )
	{
		$sql = ' _class = ?,
			`filename` = ?,
			`startLine` = ?,
			`endLine` = ?,
			`docCommentLine` = ?
			';

		$found = Index\Index_PDO::exists(
			$pdo,
			'functionName',
			$entity->functionName(),
			'shrinkpress_functions'
			);

		if (!$found)
		{
			$sql = 'INSERT IGNORE INTO shrinkpress_functions SET '
				. $sql . ', `functionName` = ? ';
		} else
		{
			$sql = 'UPDATE shrinkpress_functions SET '
				. $sql . ' WHERE `functionName` = ? ';
		}

		$q = $pdo->prepare( $sql );

		$data = $entity->jsonSerialize();
		$q->execute(array(
			get_class($entity),
			$data['filename'],
			$data['startLine'],
			$data['endLine'],
			$data['docCommentLine'],
			$data['functionName'],
		));
	}

	static function all(\PDO $pdo)
	{
		return Index\Index_PDO::all( $pdo, 'functionName', 'shrinkpress_functions' );
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS shrinkpress_functions; ')->execute();
		$pdo->prepare('CREATE TABLE shrinkpress_functions (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				functionName varchar(255) NOT NULL,
				filename varchar(255) NOT NULL DEFAULT "",
				`startLine` int(11) NOT NULL DEFAULT 0,
				`endLine` int(11) NOT NULL DEFAULT 0,
				docCommentLine int(11) NOT NULL DEFAULT 0,
				_class varchar(255) NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY functionName (functionName)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
