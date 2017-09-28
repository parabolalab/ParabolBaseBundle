<?php

namespace Parabol\BaseBundle\Entity\Base;

use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
* Base
*/
trait Base
{		

	public function __call($property, $arguments)
    {	
    	if(method_exists($this, 'getTranslations')) return $this->proxyCurrentLocaleTranslation('get'.ucfirst($property), $arguments);
    	else return call_user_method_array($property, $this, $arguments);
    }

    public function __get($property)
    {
        if(property_exists($this, $property)) return $this->{'get'.ucfirst($property)}();
        else return $this->__call($property, array());
    }

    public function __toString()
    {
        return isset($this->title) ? $this->title : (string)$this->id;
    }

}