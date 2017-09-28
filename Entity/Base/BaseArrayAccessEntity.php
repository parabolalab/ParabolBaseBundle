<?php

namespace Parabol\BaseBundle\Entity\Base;

class BaseArrayAccessEntity extends BaseEntity implements \ArrayAccess 
{
	public function __construct(array $values = null)
    {
        foreach($values as $key => $value) $this->offsetSet($key, $value);
    }

	public function offsetExists ( $offset )
	{
		return property_exists($this, $offset);
	}
	public function offsetGet ( $offset )
	{
		return $this->offsetExists($offset) ? $this->{$offset} : null;
	}
	public function offsetSet ( $offset , $value )
	{
		if($this->offsetExists($offset)) $this->{$offset} = $value;
	}
	public function offsetUnset ( $offset )
	{
		if($this->offsetExists($offset)) $this->{$offset} = null;
	}

}