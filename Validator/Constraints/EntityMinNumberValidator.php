<?php
 
namespace Parabol\BaseBundle\Validator\Constraints;
 
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
 
class EntityMinNumberValidator extends ConstraintValidator
{
    protected $requestStack;
    public static $error = 'aaaaaa';
     
    public function __construct($requestStack) //\Doctrine\ORM\EntityManager $em
    {
        $this->requestStack = $requestStack;
    }
     
    public function validate($value, Constraint $constraint)
    {

        $ns = preg_replace('/^data\./', '', $this->context->getPropertyPath());

        $request = $this->requestStack->getCurrentRequest();

        $allParams = $request->request->all();

        if($constraint->ns) {
            $collection = isset($allParams[$constraint->ns]) ? $allParams[$constraint->ns] : [];
        }

        if($constraint->min && count($collection) < $constraint->min)
        {
            $this->context->buildViolation($constraint->minMessage)
                        ->setParameter('{{ limit }}', $constraint->min)
                        ->addViolation(); 
        }

        if($constraint->max && count($collection) > $constraint->max)
        {
            $this->context->buildViolation($constraint->maxMessage)
                        ->setParameter('{{ limit }}', $constraint->max)
                        ->addViolation(); 
        }
        

         
    }
}