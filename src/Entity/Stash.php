<?php

namespace ShrinkPress\Build\Entity;

use ShrinkPress\Build\Assist;

class Stash
{
	use Assist\Instance;

	protected $umbrella;

	function setStash(Assist\Umbrella $umbrella)
	{
		$this->umbrella = $umbrella;
	}

	function exists($filename)
	{
		if (empty($this->umbrella))
		{
			throw new \RuntimeException(
				'No source found, set one with '
					. 'ShrinkPress\Build\Entity\Stash::setStash()'
			);
		}

		return $this->umbrella->exists( $filename );
	}

	function read($filename)
	{
		if (empty($this->umbrella))
		{
			throw new \RuntimeException(
				'No source found, set one with '
					. 'ShrinkPress\Build\Entity\Stash::setStash()'
			);
		}

		return $this->umbrella->read( $filename );
	}

	function write($filename, $contents)
	{
		if (empty($this->umbrella))
		{
			throw new \RuntimeException(
				'No source found, set one with '
					. 'ShrinkPress\Build\Entity\Stash::setStash()'
			);
		}

		return $this->umbrella->write( $filename, $contents );
	}

	function unlink($filename)
	{
		if (empty($this->umbrella))
		{
			throw new \RuntimeException(
				'No source found, set one with '
					. 'ShrinkPress\Build\Entity\Stash::setStash()'
			);
		}

		return $this->umbrella->unlink( $filename );
	}
}
