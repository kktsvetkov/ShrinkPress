<?php

namespace ShrinkPress\Build\Entity;

use ShrinkPress\Build\Assist;

class Source
{
	use Assist\Instance;

	protected $umbrella;

	function setSource(Assist\Umbrella $umbrella)
	{
		$this->umbrella = $umbrella;
	}

	function exists($filename)
	{
		if (empty($this->umbrella))
		{
			throw new \RuntimeException(
				'No source found, set one with '
					. 'ShrinkPress\Build\Entity\Source::setSource()'
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
					. 'ShrinkPress\Build\Entity\Source::setSource()'
			);
		}

		return $this->umbrella->read( $filename );
	}
}
