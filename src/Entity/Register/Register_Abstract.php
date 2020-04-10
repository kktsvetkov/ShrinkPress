<?php

namespace ShrinkPress\Build\Entity\Register;

use ShrinkPress\Build\Entity\Stash;

abstract class Register_Abstract Implements \JsonSerializable
{
	protected $register = array();

	protected $registerType;

	function __construct()
	{
		$this->registerType = strtolower(
			str_replace(
				'ShrinkPress\\Build\\Entity\\Register\\',
				'',
				get_called_class()
				)
			);

		$this->load();
	}

	protected function getEntities()
	{
		return $this->register;
	}

	protected function getKeys()
	{
		return array_keys($this->register);
	}

	protected function addEntity($key, $entity)
	{
		$key = (string) $key;
		if (empty($this->register[ $key ]))
		{
			$this->register[ $key ] = $entity;
		}

		return $this;
	}

	protected function getEntity($key)
	{
		$key = (string) $key;
		if (!empty($this->register[ $key ]))
		{
			return $this->register[ $key ];
		}

		return null;
	}

	protected function entityClasses()
	{
		$entityClasses = array();
		foreach ($this->register as $entity)
		{
			$entityClasses[] = get_class($entity);
		}

		return $entityClasses;
	}

	function jsonSerialize()
	{
		$data = array();
		foreach ($this->register as $key => $entity)
		{
			$data[ $key ] = get_class($entity);
		}

		return $data;
	}

	protected function stashFolder()
	{
		return $this->registerType . '/';
	}

	protected function stashFilename()
	{
		return 'register.'
			. $this->registerType
			. '.json';
	}

	protected function stashEntityFilename($key)
	{
		return $key . '.json';
	}

	function load()
	{
		$stash = Stash::instance();
		if (!$stash->exists( $stashFilename = $this->stashFilename() ))
		{
			return false;
		}

		if (!$json = $stash->read( $stashFilename ))
		{
			return false;
		}

		$data = (array) json_decode($json, true);
		if (!$data)
		{
			return false;
		}

		$stashFolder = $this->stashFolder();
		foreach ($data as $key => $entityClass)
		{
			$stashEntity = $stashFolder . $this->stashEntityFilename($key);

			if (!$stash->exists( $stashEntity ))
			{
				continue;
			}

			if (!$json = $stash->read( $stashEntity ))
			{
				continue;
			}

			$entity = new $entityClass( $key );
			$this->addEntity($key, $entity);

			$data = (array) json_decode($json, true);
			if (!$data)
			{
				continue;
			}

			if (is_callable([$entity, 'load']))
			{
				$entity->load($data);
			}
		}
	}

	const json_encode_options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;

	function save()
	{
		$stash = Stash::instance();
		$stash->write(
			$this->stashFilename(),
			json_encode(
				$this->jsonSerialize(),
				self::json_encode_options
				)
			);

		$stashFolder = $this->stashFolder();
		foreach ($this->register as $key => $entity)
		{
			$stash->write(
				$stashFolder . $this->stashEntityFilename($key) ,
				json_encode(
					$entity->jsonSerialize(),
					self::json_encode_options
					)
				);
		}
	}

	function __destruct()
	{
		$this->save();
	}
}
