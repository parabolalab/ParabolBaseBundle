<?php
 
namespace Parabol\BaseBundle\Validator\Constraints;
 
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
 
class RangeFieldsValidator extends ConstraintValidator
{
    protected $em;
     
    public function __construct() //\Doctrine\ORM\EntityManager $em
    {
        // $this->em = $em;
    }
     
    public function validate($value, Constraint $constraint)
    {
        $data = $this->context->getRoot()->getData();

        $from = $data->{'get'.ucfirst($constraint->from)}();

        if($value && $from)
        {
            if($value <= $from) 
            {
                $this->context->buildViolation(sprintf($constraint->message, $constraint->from))
                        ->addViolation(); 
            }
        }
        
    }
}