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

	public static function merge($merged, $array, $lastkey = null)
	{


	 	foreach($array as $key => $item)
	 	{
	 		if(!isset($merged[$key]))
	 		{
	 			$merged[$key] = $item;
	 		}
	 		else
	 		{
	 			if(!is_integer($key))
	 			{
		 			if(!is_array($item)) $merged[$key] =  $item;
		 			else $merged[$key] = static::merge($merged[$key], $item, $key);
		 		}
	 			else {

	 				$merged = array_merge($merged, $array);
	 				$serialized = '';
	 				$new = [];
	 				foreach ($merged as $key => $value) {
	 					$serializedItem = serialize($value);
	 					if(strpos($serialized, $serializedItem) === false)
	 					{
	 						$serialized .= $serializedItem;
	 						$new[] = $value;
	 					}
	 					
	 				}
	 				$merged = $new;
	 				break;
	 			}
	 		}
	 	}

	 	return $merged;
	}

}