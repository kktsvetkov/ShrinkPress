<?php

namespace ShrinkPress\Build\Project\Storage;

use ShrinkPress\Build\Project\Entity;

class MySQL extends StorageAbstract
{
	protected $mysql;

	function __construct(\mysqli $mysql)
	{
		$this->mysql = $mysql;
	}

	function beforeScan()
	{
		$this->wipe();
		$this->setup();
	}

	function afterScan() {}

	protected function q($sql)
	{
		if (!$r = $this->mysql->query($sql))
		{
			return false;
		}

		if (true === $r)
		{
			return true;
		}

		return $r->fetch_all(MYSQLI_ASSOC);
	}

	function write($entity, $name, array $data)
	{
		switch ($entity)
		{
			case self::ENTITY_FUNCTION:
				return $this->writeFunction($name, $data);
				break;

			default:
				throw new \UnexpectedValueException(
					"Weird entity: {$entity}"
				);
		}
	}

	protected function existsFunction($name)
	{
		$sql = 'SELECT * FROM shrinkpress_functions WHERE name = "'
			. $this->mysql->escape_string($name)
			. '" limit 0, 1';
		return ($found = $this->q($sql))
			? $found[0]
			: false;
	}

	function readFunction($name)
	{
		$object = new Entity\WpFunction($name);

		if ($found = $this->existsFunction($name))
		{
			$callers = $this->q(
				'SELECT * FROM shrinkpress_functions_calls WHERE function_id = '
					. $found['id']
				);
			$found['callers'] = [];
			foreach ($callers as $caller)
			{
				$found['callers'][] = array(
					$caller['file'],
					$caller['line'],
				) + (!empty($caller['caller'])
					? array(3 => $caller['caller'])
					: array()
					);
			}

			$object->load($found);
		}

		return $object;
	}

	function writeFunction(Entity\WpFunction $entity)
	{

		$data = $entity->getData();
		$name = $data['name'];

		$sql = '';

		$callers = $data['callers'];
		unset($data['callers']);

		unset($data['name']);
		unset($data['isPrivate']);
		unset($data['code']);
		foreach ($data as $k => $v)
		{
			if (is_null($v))
			{
				continue;
			}

			$v = is_int($v)
				? $v
				: '"' . $this->mysql->escape_string($v) . '"';

			$sql .= " {$k} = {$v}, ";
		}
		$sql .= ' name = "' . $this->mysql->escape_string($name) . '" ';

		if (!$found = $this->existsFunction($name))
		{
			$sql = 'INSERT INTO shrinkpress_functions SET ' . $sql . '; ';
		} else
		{
			$sql = 'UPDATE shrinkpress_functions SET ' . $sql . ' WHERE id = '
				. $found['id']
				. ' ';
		}

		$this->q($sql);
		if ($callers)
		{
			if (!$found)
			{
				$found = $this->existsFunction($name);
			}

			foreach ($callers as $c)
			{
				$this->q(
					'INSERT IGNORE INTO shrinkpress_functions_calls (
						function_id, file, line, caller
					) VALUES ('
					. $found['id'] . ', "'
					. $this->mysql->escape_string( $c[0] ) . '", '
					. $c[1] . ', "'
					. (!empty($c[2])
						? $this->mysql->escape_string( $c[2] )
						: ''
					)
					. '" );'
				);
			}
		}
	}

	function getFunctions()
	{
		$funcs = $this->q(
			'SELECT name FROM shrinkpress_functions ORDER BY name'
			);
		return array_column($funcs, 'name');
	}

	protected function wipe()
	{
		$this->q(' DROP TABLE IF EXISTS shrinkpress_functions; ');
		$this->q(' DROP TABLE IF EXISTS shrinkpress_functions_calls; ');
	}

	protected function setup()
	{
		$this->q('CREATE TABLE shrinkpress_functions (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				name varchar(255) NOT NULL,
				fileOrigin varchar(255) NOT NULL DEFAULT "",
				startLine int(11) NOT NULL DEFAULT 0,
				endLine int(11) NOT NULL DEFAULT 0,
				docComment text NOT NULL,
				docCommentLine int(11) NOT NULL DEFAULT 0,
				classNamespace varchar(255) NOT NULL DEFAULT "",
				className varchar(255) NOT NULL DEFAULT "",
				classMethod varchar(255) NOT NULL DEFAULT "",
				classFile varchar(255) NOT NULL DEFAULT "",
			PRIMARY KEY (id),
			UNIQUE KEY name (name),
			KEY classID (classNamespace, className)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ');

		$this->q('CREATE TABLE shrinkpress_functions_calls (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				function_id bigint(20) unsigned NOT NULL,
				file varchar(255) NOT NULL,
				line int(11) NOT NULL,
				caller varchar(255) NULL,
			PRIMARY KEY (id),
			UNIQUE KEY origin (function_id, file, line),
			KEY function_id (function_id),
			KEY caller (caller)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ');
	}
}
