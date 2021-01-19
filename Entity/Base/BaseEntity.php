<?php

namespace Parabol\BaseBundle\Entity\Base;

use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use DateTime;
use DateTimeInterface;

/**
* BaseEntity
*/
class BaseEntity
{		
    use TimestampableTrait
    ;

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt ? $this->updatedAt : new DateTime();
    }

    public function __construct()
    {
        if(method_exists($this, 'setHash')) $this->setHash(sha1(uniqid()));
    }

    public function __toString()
    {
        if(method_exists($this, 'getTitle')) $result = $this->getTitle();
        elseif(method_exists($this, 'getName')) $result = $this->getName();
        else $result = $this->id;
        return (string) $result;
    }

    public function __call($property, $arguments)
    {   
        if(($method = preg_replace('/AsString$/','',$property)) != $property)
        {
            return $this->valueToString(call_user_func([$this, $method]));
        }
        elseif(method_exists($this, 'getTranslations')) return $this->proxyCurrentLocaleTranslation('get'.ucfirst(preg_replace('/^get/','',$property)), $arguments);
        
        

        // else return call_user_method_array($property, $this, $arguments);
    }

    public function __get($property)
    {
        if(property_exists($this, $property)) return $this->{'get'.ucfirst($property)}();
        else return $this->__call($property, array());
    }

    public function valueToString($value)
    {
        switch(gettype($value))
        {
            case 'array':
                $value = implode(', ', $value);
            break;
            default:
                $value =  (string) $value;
        }
        return $value;
    }

}