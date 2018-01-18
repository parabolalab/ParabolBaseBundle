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
        $firstKey = key($allParams);
        
        if($constraint->min > 0 && !isset($allParams[$firstKey][$ns]) ||  isset($allParams[$firstKey][$ns]) && count($allParams[$firstKey][$ns]) < $constraint->min)
        {
            $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ limit }}', $constraint->min)
                        ->addViolation(); 
        }

        

         
    }
}