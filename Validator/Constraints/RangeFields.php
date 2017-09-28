<?php

namespace Parabol\BaseBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RangeFields extends Constraint
{
    public $message = 'The value must be greater than the value of the field "%s"';
    public $from;

    public function __construct($options = null) //\Doctrine\ORM\EntityManager $em
    {
        parent::__construct($options);
        
        if (null === $this->from) {
            throw new MissingOptionsException(sprintf('Option "from" must be given for constraint %s', __CLASS__), array('field'));
        }
    }

    public function validatedBy()
	{
	    return get_class($this).'Validator';
	}

}