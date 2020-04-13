<?php

namespace ShrinkPress\Build\Entity\Globals;

use ShrinkPress\Build\Entity;

class WordPress_Global implements Global_Entity
{
	use Entity\Load;

	protected $globalName;

	function __construct($globalName)
	{
		$this->globalName = (string) $globalName;
	}

	function globalName()
	{
		return $this->globalName;
	}

	const MENTION_ARRAY = 'array';
	const MENTION_KEYWORD = 'keyword';

	protected $mentions = array();

	function addMention(Entity\Files\File_Entity $file, $line, $mentionType = self::MENTION_KEYWORD)
	{
		$filename = $file->filename();
		$line = (int) $line;

		$mention = "{$filename}:{$line}";
		$this->mentions[ $mention ] = array($filename, $line, $mentionType);

		$file->addGlobal( $this, $line );
	}

}
