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

        $request = $this->requestStack->getCurrentRequest();

        $entities = $request->request->get('uploaded_files');
        
        if(count($entities) < $constraint->min)
        {
             $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ limit }}', $constraint->min)
                        ->addViolation(); 
        }

         
    }
}