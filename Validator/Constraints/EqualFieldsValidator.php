<?php
 
namespace Parabol\BaseBundle\Validator\Constraints;
 
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
 
class EqualFieldsValidator extends ConstraintValidator
{
    protected $em;
    private static $values = [];
    
     
    public function __construct() //\Doctrine\ORM\EntityManager $em
    {
        // $this->em = $em;
    }
     
    public function validate($value, Constraint $constraint)
    {
        $data = $this->context->getRoot()->getData();

        foreach($constraint->fields as $i => $field)
        {
            if($i)
            {
                if($data->{'get'.ucfirst($field)}() != $value)
                {
                       $this->context->buildViolation(sprintf('Fields "%s" must have same value', implode('" and "', $constraint->fields)))
                        ->addViolation(); 

                        return;
                }
            }
            else $value = $data->{'get'.ucfirst($field)}();
        }
    }
}