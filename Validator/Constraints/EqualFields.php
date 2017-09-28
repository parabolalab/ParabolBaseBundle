<?php

namespace Parabol\BaseBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EqualFields extends Constraint
{
    public $message = 'The string "%string%" contains an illegal character: it can only contain letters or numbers.';
    public $fields;

    public function __construct($options = null) //\Doctrine\ORM\EntityManager $em
    {
        parent::__construct($options);
        
        if (null === $this->fields) {
            throw new MissingOptionsException(sprintf('Option "fields" must be given for constraint %s', __CLASS__), array('field'));
        }
    }

    public function validatedBy()
	{
	    return get_class($this).'Validator';
	}

}