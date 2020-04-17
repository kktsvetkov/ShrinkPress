<?php

namespace ShrinkPress\Build\Index\PDO;

use ShrinkPress\Build\Entity;
use ShrinkPress\Build\Index;

class Files
{
	static function read( $filename, \PDO $pdo )
	{
		$data = Index\Index_PDO::exists(
			$pdo,
			'filename',
			$filename,
			'shrinkpress_files'
			);

		if (!empty($data['_class']))
		{
			$entity = new $data['_class']( $filename );
		} else
		{
			$entity = Entity\Files\WordPress_PHP::factory( $filename );
		}

		return $entity->load( $data );
	}

	static function write( Entity\Files\File_Entity $entity, \PDO $pdo )
	{
		$sql = ' `_class` = ?,
			`docPackage` = ?,
			`docSubPackage` = ?
			';

		$found = Index\Index_PDO::exists(
			$pdo,
			'filename',
			$entity->filename(),
			'shrinkpress_files'
			);

		if (!$found)
		{
			$sql = 'INSERT IGNORE INTO shrinkpress_files SET '
				. $sql . ', `filename` = ? ';
		} else
		{
			$sql = 'UPDATE shrinkpress_files SET '
				. $sql . ' WHERE `filename` = ? ';
		}

		$q = $pdo->prepare( $sql );

		$data = $entity->jsonSerialize();
		$q->execute(array(
			get_class($entity),
			$data['docPackage'],
			$data['docSubPackage'],
			$data['filename'],
		));
	}

	static function all(\PDO $pdo)
	{
		return Index\Index_PDO::all( $pdo, 'filename', 'shrinkpress_files' );
	}

	static function packages(\PDO $pdo)
	{
		$q = $pdo->query(
			'SELECT concat(docPackage, ".", docSubPackage) as fullPackageName '
				. ' FROM shrinkpress_files '
				. ' GROUP BY fullPackageName '
				. ' ORDER BY fullPackageName;'
			);

		return $q->fetchAll($pdo::FETCH_COLUMN, 0);
	}

	static function clean(\PDO $pdo)
	{
		$pdo->prepare(' DROP TABLE IF EXISTS shrinkpress_files; ')->execute();
		$pdo->prepare('CREATE TABLE shrinkpress_files (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				filename varchar(255) NOT NULL,
				docPackage varchar(255) NOT NULL DEFAULT "",
				docSubPackage varchar(255) NOT NULL DEFAULT "",
				_class varchar(255) NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY filename (filename)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
