<?php 

namespace Parabol\BaseBundle\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\AdminCoreBundle\Entity\AppVar;

abstract class BaseData implements FixtureInterface
{
	  protected $manager;
    
	  public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->loadData();

    }

    public function createObjects($class, $entitiesValues)
    {

        foreach ($entitiesValues  as $entityValues) {
            $entity = new $class();

            foreach($entityValues as $field => $fieldValue)
            {
              if($field === 'translations')
              {
                  foreach($fieldValue as $lang => $values)
                  {
                      foreach($values as $key => $value)
                      {
                          $entity->translate($lang)->{'set' . ucfirst($key)}($value);
                      }
                  }

                  $entity->mergeNewTranslations();
              }
              else
              {
                  $entity->{'set' . ucfirst($field)}($fieldValue);
              }
            }

            
            $this->manager->persist($entity);
            $this->manager->flush($entity);
        }
    }

    public function loadData()
    {
      
    }
}