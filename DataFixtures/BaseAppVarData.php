<?php 

namespace Parabol\BaseBundle\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\AdminCoreBundle\Entity\AppVar;

class BaseAppVarData implements FixtureInterface
{
	protected $manager;
    protected $namespace = null;
    protected $namespaceLabels = [];

	public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function addNamespace($name, $namespaceLabels)
    {
        $this->namespace = $name;
        $this->namespaceLabels = $namespaceLabels;
        return $this;
    }

    public function addAppVar($app, $propertyName, $translations, $params = [])
    {

        $params = array_merge(['required' => false, 'grid' => 6, 'i18n' => false, 'readonly' => false, 'varType' => 'string', 'cssClass' => null, 'twigAlias' => null, 'postAction' => null, 'fieldOptions' => null], $params);
        $appVar = new AppVar();
        $appVar->setNamespace($this->namespace);
        $appVar->setApp($app);
        $appVar->setPropertyName($propertyName);

        foreach($translations as $lang => $values)
        {
             $appVar->translate($lang)->setNamespaceLabel($this->namespaceLabels[$lang]);
             foreach($values as $key => $value)
             {
                    $method = 'set'.ucfirst($key);
                    $appVar->translate($lang)->$method($value); 
             }            
        }

        $appVar->setCssClass($params['cssClass']);
        $appVar->setVarType($params['varType']);
        $appVar->setI18n($params['i18n']);
        $appVar->setGrid($params['grid']);    
        $appVar->setIsRequired($params['required']);    
        $appVar->setIsReadonly((boolean)$params['readonly']);
        $appVar->setTwigAlias($params['twigAlias']); 
        $appVar->setPostAction($params['postAction']);
        $appVar->setFieldOptions($params['fieldOptions']); 

        
        $appVar->mergeNewTranslations();

        $this->manager->persist($appVar);

        return $this;
    }
}