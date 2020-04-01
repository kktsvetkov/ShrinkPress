<?php

namespace ShrinkPress\Build\Storage;

use ShrinkPress\Build\Project\Entity;

class PDO extends StorageAbstract
{
	protected $pdo;

	function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	function beforeScan()
	{
		$this->wipe();
		$this->setup();
	}

	function afterScan() {}

	protected function existsFunction($name)
	{
		$sql = 'SELECT * FROM pdo_shrinkpress_functions WHERE name = ? LIMIT 0, 1';
		$q = $this->pdo->prepare($sql);
		$q->execute([ $name ]);

		if (!$q->rowCount())
		{
			return false;
		}

		return $q->fetch($this->pdo::FETCH_ASSOC);
	}

	function readFunction($name)
	{
		$object = new Entity\WpFunction($name);

		if ($found = $this->existsFunction($name))
		{
			$sql = 'SELECT * FROM pdo_shrinkpress_functions_calls WHERE function_id = ? ';

			$q = $this->pdo->prepare( $sql );
			$q->execute([ $found['id'] ]);

			$callers = $q->fetchAll($this->pdo::FETCH_ASSOC);
			foreach ($callers as $caller)
			{
				$found['callers'][] = array(
					$caller['file'],
					$caller['line'],
				) + (!empty($caller['caller'])
					? array(2 => $caller['caller'])
					: array()
					);
			}

			$object->load($found);
		}

		return $object;
	}

	function writeFunction(Entity\WpFunction $entity)
	{
		$sql = ' `fileOrigin` = ?,
			`startLine` = ?,
			`endLine` = ?,
			`docCommentLine` = ?,
			`classNamespace` = ?,
			`className` = ?,
			`classMethod` = ?,
			`classFile` = ?
			';

		if (!$found = $this->existsFunction($entity->name))
		{
			$sql = 'INSERT IGNORE INTO pdo_shrinkpress_functions SET ' . $sql . ', `name` = ? ';
		} else
		{
			$sql = 'UPDATE pdo_shrinkpress_functions SET ' . $sql . ' WHERE `name` = ? ';
		}

		$q = $this->pdo->prepare($sql);
		$q->execute(array(
			$entity->fileOrigin,
			$entity->startLine,
			$entity->endLine,
			$entity->docCommentLine,
			$entity->classNamespace,
			$entity->className,
			$entity->classMethod,
			$entity->classFile,
			$entity->name,
		));

		if (!$entity->callers)
		{
			return;
		}

		if (!$found)
		{
			$found = $this->existsFunction($entity->name);
		}

		foreach ($entity->callers as $call)
		{
			$sql = 'INSERT IGNORE INTO pdo_shrinkpress_functions_calls
				( function_id, file, line, caller) VALUES (?, ?, ?, ?); ';

			$q = $this->pdo->prepare($sql);
			$q->execute([
				$found['id'],
				$call[0],
				$call[1],
				!empty($call[2])
					? $call[2]
					: ''
			]);
		}
	}

	function getFunctions()
	{
		$q = $this->pdo->query(
			'SELECT name FROM pdo_shrinkpress_functions ORDER BY name'
			);
		return $q->fetchAll($this->pdo::FETCH_COLUMN, 0);
	}

	protected function wipe()
	{
		$this->pdo->prepare(' DROP TABLE IF EXISTS pdo_shrinkpress_functions; ')->execute();
		$this->pdo->prepare(' DROP TABLE IF EXISTS pdo_shrinkpress_functions_calls; ')->execute();
	}

	protected function setup()
	{
		$this->pdo->prepare('CREATE TABLE pdo_shrinkpress_functions (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				name varchar(255) NOT NULL,
				fileOrigin varchar(255) NOT NULL DEFAULT "",
				startLine int(11) NOT NULL DEFAULT 0,
				endLine int(11) NOT NULL DEFAULT 0,
				docCommentLine int(11) NOT NULL DEFAULT 0,
				classNamespace varchar(255) NOT NULL DEFAULT "",
				className varchar(255) NOT NULL DEFAULT "",
				classMethod varchar(255) NOT NULL DEFAULT "",
				classFile varchar(255) NOT NULL DEFAULT "",
			PRIMARY KEY (id),
			UNIQUE KEY name (name),
			KEY classID (classNamespace, className)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();

		$this->pdo->prepare('CREATE TABLE pdo_shrinkpress_functions_calls (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				function_id bigint(20) unsigned NOT NULL,
				file varchar(255) NOT NULL,
				line int(11) NOT NULL,
				caller varchar(255) NULL,
			PRIMARY KEY (id),
			UNIQUE KEY origin (function_id, file, line),
			KEY function_id (function_id),
			KEY caller (caller)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ')->execute();
	}
}
