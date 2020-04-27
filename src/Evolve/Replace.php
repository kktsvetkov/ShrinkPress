<?php

namespace ShrinkPress\Reframe\Evolve;

class Replace
{
	const marker = '%SHRINKPRESSREPLACE%';
	static function replaceFunction(Parse $parser, array $f, array $m, $replaceFunc)
	{
		$inside = ('replaceCall' == $replaceFunc)
			? HasInside::$calls
			: HasInside::$hooks;

		if (empty($inside[ $f['function'] ]))
		{
			return false;
		}

		foreach ($inside[ $f['function'] ] as $filename => $i)
		{
			echo "D0.{$replaceFunc} {$f['function']}() in {$filename}\n";

			$code = file_get_contents( $filename );
			$nodes = $parser->parse($code);

			$replace = ('replaceCall' == $replaceFunc)
				? new ReplaceCalls($f['function'])
				: new ReplaceHooks($f['function']);

			if ($matches = $parser->traverse($replace, $nodes))
			{
				$lines = explode("\n", $code);

				foreach ($matches as $line)
				{
					$lines[ $line-1 ] = Code::$replaceFunc(
						$lines[ $line-1 ],
						$f['function'],
						self::marker
					);
				}

				$code = join("\n", $lines);
			}

			$code = str_replace(self::marker, '\\' . $m['full'], $code);
			file_put_contents($filename, $code);
		}

		return true;
	}

}
