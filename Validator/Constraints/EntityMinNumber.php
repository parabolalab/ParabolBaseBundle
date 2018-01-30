<?php

namespace Parabol\BaseBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EntityMinNumber extends Constraint
{
    public $minMessage = 'This collection can not contain less than {{ limit }} elements.';
    public $maxMessage = 'This collection can not contain more than {{ limit }} elements.';
    public $min = null;
    public $max = null;
    public $name = null;
    public $ns = null;
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