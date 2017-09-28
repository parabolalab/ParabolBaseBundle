<?php

namespace Parabol\BaseBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EntityMinNumber extends Constraint
{
    public $message = 'This collection must contain {{ limit }} element.';
    public $min = 1;
    public $name = null;
    public $locales = array();

    public function __construct($options = null) //\Doctrine\ORM\EntityManager $em
    {

        parent::__construct($options);
        
        // if (null === $this->fields) {
        //     throw new MissingOptionsException(sprintf('Option "fields" must be given for constraint %s', __CLASS__), array('field'));
        // }
    }

    public function validatedBy()
	{
	    return get_class($this).'Validator';
	}

}