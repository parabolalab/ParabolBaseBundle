<?php

namespace Parabol\BaseBundle\Component\Yaml;

use Symfony\Component\Yaml\Yaml as BaseYaml;

/**
* Yaml
*/
class Yaml extends BaseYaml
{
	
	public static function mergeDuplicates($arr)
	{
		foreach($arr as $key => $value)
		{
				if(is_int($key))
				{
					$new = [];
					$temp = '';
					foreach($arr as $value)
					{
						$serialized = (serialize($value));
						if(strpos($temp, $serialized) === false)
						{
							$temp .= $serialized;
							$new[] = $value;
						}
					}

					return $new;
				}
				elseif(is_array($value))
				{
					$arr[$key] = static::mergeDuplicates($arr[$key]);
				}
			
		}

		return $arr;
	}

	public static function dump($input, $inline = 2, $indent = 4, $flags = 0)
    {
    	return parent::dump(static::mergeDuplicates($input), $inline, $indent, $flags);
    }


}