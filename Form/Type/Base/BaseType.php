<?php

namespace Parabol\BaseBundle\Form\Type\Base;

use Admingenerated\ParabolAdminCoreBundle\Form\BasePageType\EditType as BaseEditType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * EditType
 */
trait BaseType
{
    protected $builder;


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('builderExtensions', []);
        $resolver->setDefault('ckeditor', []);
    }

    public function getBuilder()
    {
        return $this->builder;
    }

    public function optionsFixer($options)
    {
        //symfony before 3.0 fix
        unset($options['entry_options'], $options['entry_type']);
        return $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->builder = $builder;

        if(is_object($options['builderExtensions']))
        {
            foreach($options['builderExtensions']->getExtensions() as $ext)
            {
                $ext->configureOptions($this, $options);
                $ext->preBuild($this, $options);
            }
        }   

        parent::buildForm($this->builder, $options);

        if(is_object($options['builderExtensions']))
        {
            foreach($options['builderExtensions']->getExtensions() as $ext)
            {
                $ext->postBuild($this, $options);
            }
        }

        // dump($this->getDataClass() , $builder, $options);
    }

    public function getFieldOptons($name)
    {
        $fieldOptions = [];
        if(method_exists($this, 'getOptions' . ucfirst($name)))
        {
            $fieldOptions = $this->{'getOptions' . ucfirst($name)}();
        }
        return $fieldOptions;
    }

    public function forceAdd($name, $typeClass, array $fieldOptions = [], array $builderOption = [])
    {
        $this->builder->add($name, $typeClass, $this->getOptions($name, $fieldOptions, $builderOption), $typeClass);   
    }

    public function getData()
    {
        return $this->builder->getData();
    }

    public function getDataClass()
    {
        return $this->builder->getDataClass();
    }

    public function getOptions($name, array $fieldOptions = array(), array $builderOptions = array(), $forceType = null)
    {
        
        $optionsClass = preg_replace('/Entity(\\\\[^\\\\]+)/', 'Form\Type$1\Options', $this->getDataClass());                                        
        $options = class_exists($optionsClass) ? new $optionsClass() : null;

        return $this->resolveOptions(
            $name, 
            $fieldOptions, 
            $builderOptions, 
            $options,
            $forceType
        );
    
    }

 //    public function getOptionsId(array $builderOptions = array())
 //    {
 //        if(method_exists(get_parent_class(), 'getOptionsId')) $result = parent::getOptionsId($builderOptions);
 //        else $result = array();
        
 //        if(!isset($result['disabled'])) $result['disabled'] = true;

 //        return $result;
 //    }

    protected function getOptionsTextBlocksTranslations(array $builderOptions = array())
    {
        return $this->getOptionsTranslations($builderOptions);
    }

    protected function getOptionsTranslations(array $builderOptions = array())
    {

        // var_dump($builderOptions);

        if(method_exists(get_parent_class(), 'getOptionsTranslations')) $result = parent::getOptionsTranslations($builderOptions);
        else $result = array();
        $default_fields = $this->getDefaultTranslationFieldsSetting($builderOptions);

        // var_dump($result, $default_fields);

        unset($result['allow_add'], $result['allow_delete'], $result['type'], $result['options']);

        $result = $this->optionsFixer($result);

      
        if(isset($result['fields']))
        {
            foreach($default_fields as $name => $values)
            {
                if(isset($result['fields'][ $name ])) $result['fields'][ $name ] = array_merge($values, $result['fields'][ $name ]);
                else $result['fields'][ $name ] = $values;
            }
        }
        else $result['fields'] = $default_fields;


        
        $result['label'] = ' ';

        $class = $this->getDataClass().'Translation';




        if(method_exists($class, 'formWithoutFields'))
        {
            if(isset($result['exclude_fields'])) $result['exclude_fields'] = array_merge($result['exclude_fields'], $class::formWithoutFields());
            else $result['exclude_fields'] = $class::formWithoutFields();
        } 

        if(!isset($result['exclude_fields'])) $result['exclude_fields'] = [];
        else $result['exclude_fields'] = (array) $result['exclude_fields'];
        if(method_exists($class, 'setSlug')) $result['exclude_fields'][] =  'slug';

        if(isset($result['exclude_fields']))
        {
            foreach($result['exclude_fields'] as $field)
            {
                if(isset($result['fields'][$field])) unset($result['fields'][$field]);
            }
        }


        foreach($result['fields'] as $name => $config)
        {
            if(isset($config['field_type']) && $config['field_type'] == \Ivory\CKEditorBundle\Form\Type\CKEditorType::class)
            {
                $result['fields'][$name] = $config;
                // array('required' => false, 'field_type' => $this->getTypeContent(), 'config' => $this->getCKEditroDefaultConfig($builderOptions), 'plugins' => $this->getCKEditorDefaultPlugins(), 'attr' => array('style' => 'height: 600px'));



                $result['fields'][$name]['base_path'] = 'admin/components/ckeditor/';
                $result['fields'][$name]['js_path'] = 'admin/components/ckeditor/ckeditor.js';
                $result['fields'][$name]['jquery_path'] = 'admin/components/ckeditor/adapters/jquery.js';
            }
        }   

        if(method_exists($class, 'fileContexts'))
        {
            $filefield = false;
            foreach($class::fileContexts() as $context => $isMultiple)
            {
                if(isset($result['fields'][$context]))
                {
                    $filefield = true;
                    $result['fields'][$context]['field_type'] = \Parabol\FilesUploadBundle\Form\Type\BlueimpType::class;
                    $result['fields'][$context]['class'] = $class;
                    $result['fields'][$context]['multiple'] = $isMultiple;
                    
                }
            }

            if($filefield)
            {
                $result['fields']['filesUpdatedAt'] = ['field_type' =>  \Symfony\Component\Form\Extension\Core\Type\HiddenType::class];
                $result['fields']['filesOrder'] = ['field_type' => \Symfony\Component\Form\Extension\Core\Type\HiddenType::class];
                $result['fields']['filesHash'] = ['field_type' => \Symfony\Component\Form\Extension\Core\Type\HiddenType::class];
            }
        }

        // dump($result['fields']);
        // die();


        return $result;
    }

    

    protected function getOptionsColor(array $builderOptions = array())
    {
        if(method_exists(get_parent_class(), 'getOptionsColor')) $result = parent::getOptionsColor($builderOptions);
        else $result = array();
        $result['attr'] = array('class' => 'colorpicker');
        return $result;
    }
// class: Parabol\AdminCoreBundle\Entity\Page
//                 empty_value: ''
//                 required: false
//                 property: title
//                 multiple: false
//                 expanded: false
//                 # query_builder: function (\Parabol\AdminCoreBundle\Entity\PageRepository $er) { return $er->findAllEnabledWithoutProperty($this->getData()); }   
    protected function getOptionsParent(array $builderOptions = array())
    {
        if(method_exists(get_parent_class(), 'getOptionsParent')) $result = parent::getOptionsParent($builderOptions);
        else $result = array();

        $defaults = array(
                'query_builder' => function ($er) { return $er->findAllEnabledWithoutProperty($this->getData()); },
                'choice_label' => function ($parent) { return $parent->getTitle(); },
                );

        foreach($defaults as $name => $value)
        {
            if(!isset($result[$name])) $result[$name] = $value;
        }
        
        return $result;
    }

    // protected function getOptionsType(array $builderOptions = array())
    // {
    //     $result = parent::getOptionsColor($builderOptions);
        
    //     return $result;
    // }

    protected function getDefaultTranslationFieldsSetting($builderOptions)
    {
  

        $result = array();



        $class = $this->getDataClass().'Translation';

        // if($class != 'Parabol\ProductBundle\Entity\ProductTranslation') var_dump($this);

        if($class == 'Parabol\ProductBundle\Form\Type\ProductAttribute\EditTypeTranslation')
        {
            $result['url'] =  array('required' => false);
        }


        if(method_exists($class, 'setName')) $result['name'] =  array();

        if(method_exists($class, 'setTitle')) $result['title'] =    array('required' => true);

        if(method_exists($class, 'setHeadline')) $result['headline'] =    array('required' => false);

        if(method_exists($class, 'setSubheadline')) $result['subheadline'] =    array('required' => false);

        if(method_exists($class, 'setSubject')) $result['subject'] =  array( 'required' => false);

        if(method_exists($class, 'setLead')) $result['lead'] =  array( 'field_type' => \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, 'required' => false);

     
        
        if(method_exists($class, 'setContent'))
        { 
            // $result['content'] =   array( 'field_type' => \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, 'required' => false);



            $result['content'] = array( 'required' => false, 'field_type' => $this->getTypeContent(), 'config' => $this->getCKEditroDefaultConfig($builderOptions), 'plugins' => $this->getCKEditorDefaultPlugins(), 'attr' => array('style' => 'height: 600px'));

             // dump($this->getDataClass() , $this->getCKEditroDefaultConfig($builderOptions));
            
        }


        // if(method_exists($class, 'setLocationDescription'))
        // { 
        //     // $result['content'] =   array( 'field_type' => \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, 'required' => false);
        //     $result['locationDescription'] = array( 'required' => false, 'field_type' => $this->getTypeContent(), 'config' => $this->getCKEditroDefaultConfig($builderOptions), 'plugins' => $this->getCKEditorDefaultPlugins(), 'attr' => array('style' => 'height: 600px'));
        // }

        if(method_exists($class, 'setDescription')) 
        {
            if(!isset($class::$nockeditor) || !$class::$nockeditor) $result['description'] =  array( 'field_type' => $this->getTypeContent(), 'config' => $this->getCKEditroDefaultConfig($builderOptions), 'plugins' => $this->getCKEditorDefaultPlugins(), 'attr' => array('style' => 'height: 600px'));
            else $result['description'] = array('attr' => array('style' => 'height: 600px'));

            $result['description']['required'] = false;
        }
          
        if(method_exists($class, 'setButtonUrl'))   $result['buttonUrl'] =    array('required' => false); 
        if(method_exists($class, 'setButtonLabel'))   $result['buttonLabel'] =    array('required' => false); 

        if(method_exists($class, 'setDisplayOnUrl'))   $result['displayOnUrl'] =    array('required' => false); 
        
        if(method_exists($class, 'setUrl')) $result['url'] =  array('required' => false);

        if(method_exists($class, 'setLabel')) $result['label'] =  array('required' => false);



        return $result;
    }

    protected function getCKEditorDefaultPlugins()
    {
        

        $subdomainDir = preg_replace('/\/[\w_]+\.php$/', '', $_SERVER['SCRIPT_NAME']);

        return array(
                    'codemirror' => array(
                        'path'     => $subdomainDir . '/bundles/paraboladmincore/js/admin/ckeditor/plugins/codemirror/',
                        'filename' => 'plugin.js',
                    ),
                    'pagebreak' => array(
                        'path'     => $subdomainDir . '/admin/components/ckeditor/plugins/pagebreak/',
                        'filename' => 'plugin.js',
                    )
                    // 'paraboltest' => array(
                    //     'path'     => '/bundles/paraboladmincore/js/admin/ckeditor/plugins/paraboltest/',
                    //     'filename' => 'plugin.js',
                    // ),
                );
    }
    protected function getCKEditroDefaultConfig($builderOptions)
    {
        return array_merge(array(
            'height' => '500px',
            'allowedContent' => true,
            'contentsCss' => '/css/admin/ckeditor_content.css',
            'entities' => false,
            'enterMode' => 2, //CKEDITOR.ENTER_BR
            'shiftEnterMode' => 1, //CKEDITOR.ENTER_P
            // 'protectedSource' => [
            //     '/<i[^>]*><\/i>/g',
            //     '/<span[^>]*><\/span>/g',
            // ],
            'toolbar' => array(
                array(
                    'name' => 'document',
                    'items' => array('Source'),
                    ),
                array(
                    'name' => 'clipboard',
                    'groups' => array('clipboard', 'undo' ), 
                    'items' => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' )
                    ),
                array(
                    'name' => 'editing',
                    'groups' => array( 'find', 'selection', 'spellchecker' ),
                    'items' => array( 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' )
                    ),
                array(
                    'name' => 'colors',
                    'items' => array( 'TextColor', 'BGColor' )
                    ),
                array(
                    'name' => 'links',
                    'items' => array( 'Link', 'Unlink', 'Anchor' )
                    ),
                array(
                    'name' => 'insert',
                    'items' => array( 'Image', 'PageBreak', 'SpecialChar', 'Iframe' )
                    ),
                array(
                    'name' => 'tools',
                    'items' => array( 'Maximize', 'ShowBlocks' )
                    ),
                '/',
                array(
                    'name' => 'basicstyles',
                    'groups' => array( 'basicstyles', 'cleanup' ),
                    'items' => array( 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' )
                    ),
                array(
                    'name' => 'paragraph',
                    'groups' => array( 'list', 'indent', 'blocks', 'align', 'bidi' ),
                    'items' => array( 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' )
                    ),
                array(
                    'name' => 'styles',
                    'items' => array( 'Styles', 'Format', 'FontSize' )
                    ),
                
            ),
            'extraPlugins' => implode(',', array_keys($this->getCKEditorDefaultPlugins())),
            // 'filebrowserBrowseUrl' => null,
            // 'filebrowserUploadUrl' => '/uploader/upload.php',
        ), $builderOptions['ckeditor']);
    }

    
    // protected function canDisplayFilesUpdatedAt($obj)
    // {
    //     return false;
    // }

    protected function getTypeId()
    {
        return \Symfony\Component\Form\Extension\Core\Type\HiddenType::class;
    }

    protected function getTypeSort()
    {
        return \Symfony\Component\Form\Extension\Core\Type\HiddenType::class;
    }

    // protected function getTypeType()
    // {
    //     return 'ext_choice';
    // }

    protected function getTypeHash()
    {
        return \Symfony\Component\Form\Extension\Core\Type\HiddenType::class;
    }

    protected function getTypeContent()
    {
        return \Ivory\CKEditorBundle\Form\Type\CKEditorType::class;
    }

    protected function getTypeDescription()
    {
        return \Ivory\CKEditorBundle\Form\Type\CKEditorType::class;
    }

    protected function getTypeTextBlockTranslations()
    {
        return \A2lix\TranslationFormBundle\Form\Type\TranslationsType::class;
    }
    protected function getTypeTranslations()
    {
        return \A2lix\TranslationFormBundle\Form\Type\TranslationsType::class;
    }    

    protected function getTypeParent()
    {
        return \Symfony\Bridge\Doctrine\Form\Type\EntityType::class;
    }
  
    protected function getTypeFile()
    {
        return $this->getTypeFiles();
    }

    // protected function getTypeFiles()
    // {
    //     var_dump(parent::getTypeFiles());
    //     die();
    //     // if (!method_exists($this, 'canDisplayFilesUpdatedAt') || $this->canDisplayFilesUpdatedAt()) 
    //     // {            
    //     //     $this->builder->add('filesUpdatedAt', $this->getTypeFilesUpdatedAt(), $this->getOptionsFilesUpdatedAt(array()));
    //     //     $this->builder->add('filesOrder', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class);
    //     // }

    //     // return \Parabol\FilesUploadBundle\Form\Type\BlueimpType::class;
    // } 


    protected function resolveOptions($name, array $fieldOptions, array $builderOptions = array(), $optionsClass = null, $forceType = null)
    {
        $this->currentType = $forceType ? $forceType : $this->{'getType'.ucfirst($name)}();

        // if($name == 'slider') $this->currentType = 'Parabol\FilesUploadBundle\Form\Type\BlueimpType';
        // var_dump($name, $this->currentType);
        $fieldOptions = parent::resolveOptions($name, $fieldOptions, $builderOptions, $optionsClass);
    // var_dump($this->{'getType'.ucfirst($name)}());
        switch($this->currentType)
        {
            case \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class:
            
                if(!array_key_exists('widget', $fieldOptions)) { 
                    $fieldOptions['widget'] = 'single_text'; 
                }
                if(!array_key_exists('format', $fieldOptions)) { 
                    $fieldOptions['format'] = 'yyyy/MM/dd HH:mm'; 
                }
                if(!array_key_exists('attr', $fieldOptions)) { 
                    $fieldOptions['attr'] = array('data-date-format' => 'YYYY/MM/DD hh:mm');
                }
                elseif(!array_key_exists('data-date-format', $fieldOptions['attr'])) { 
                    $fieldOptions['attr']['data-date-format'] = 'YYYY/MM/DD hh:mm';
                }
                $fieldOptions['attr']['class'] = (array_key_exists('class', $fieldOptions['attr']) ? $fieldOptions['attr']['class'] . ' ' : '') . ' detetimepicker';
                
            break;

            case \Symfony\Component\Form\Extension\Core\Type\DateType::class:
            
                if(!array_key_exists('widget', $fieldOptions)) { 
                    $fieldOptions['widget'] = 'single_text'; 
                }
                if(!array_key_exists('format', $fieldOptions)) { 
                    $fieldOptions['format'] = 'yyyy/MM/dd'; 
                }
                if(!array_key_exists('attr', $fieldOptions)) { 
                    $fieldOptions['attr'] = array('data-date-format' => 'YYYY/MM/DD');
                }
                elseif(!array_key_exists('data-date-format', $fieldOptions['attr'])) { 
                    $fieldOptions['attr']['data-date-format'] = 'YYYY/MM/DD';
                }

                $fieldOptions['attr']['class'] = (array_key_exists('class', $fieldOptions['attr']) ? $fieldOptions['attr']['class'] . ' ' : '') . ' detetimepicker';
                
            break;

            case \Symfony\Component\Form\Extension\Core\Type\TimeType::class:
            
                if(!array_key_exists('widget', $fieldOptions)) { 
                    $fieldOptions['widget'] = 'single_text'; 
                }
           
                if(!array_key_exists('attr', $fieldOptions)) { 
                     $fieldOptions['attr'] = array();
                }
                if(!array_key_exists('placeholder', $fieldOptions['attr']))
                {
                    $fieldOptions['attr']['placeholder'] = 'hh:mm';
                }
                if(!array_key_exists('style', $fieldOptions['attr']))
                {
                    $fieldOptions['attr']['style'] = 'width: 70px;';
                }
                $fieldOptions['attr']['class'] = (array_key_exists('class', $fieldOptions['attr']) ? $fieldOptions['attr']['class'] . ' ' : '') . ' timepicker';

                $fieldOptions['attr']['data-type'] = 'time';
                $fieldOptions['attr']['maxlength'] = strlen($fieldOptions['attr']['placeholder']);

                
            break;

            case \Symfony\Component\Form\Extension\Core\Type\CollectionType::class:
            
                if( isset($fieldOptions['entry_options']))
                {
                  if(isset($fieldOptions['entry_options']['class'])) unset($fieldOptions['entry_options']['class']);

                  if(method_exists($fieldOptions['entry_type'], 'getCKEditroDefaultConfig') && !isset($fieldOptions['entry_options']['ckeditor']) && isset($builderOptions['ckeditor'])) $fieldOptions['entry_options']['ckeditor'] = $builderOptions['ckeditor'];
                } 

                
            break;

            // case 'checkbox':
            //     $fieldOptions['required'] = false;
            // break;

            case \Parabol\AdminCoreBundle\Form\Type\ExtChoiceType::class:
                \Parabol\AdminCoreBundle\Form\Type\ExtChoiceType::$fields[] = $name;
                \Parabol\AdminCoreBundle\Form\Type\ExtChoiceType::$currentClass = $this->getDataClass();

                // $fieldOptions['choices'] = $this->getExtChoiceOptions($name);
            break;

            case \Symfony\Component\Form\Extension\Core\Type\HiddenType::class:

                    // switch($name)
                    // {
                    //     case 'createdAt':
                    //     case 'updatedAt':
                    //         // $fieldOptions['data_class'] = 'DateTime';
                    //     break;
                    // }

            break;

            case \Ivory\CKEditorBundle\Form\Type\CKEditorType::class:
                $fieldOptions['base_path'] = 'admin/components/ckeditor/';
                $fieldOptions['js_path'] = 'admin/components/ckeditor/ckeditor.js';
                $fieldOptions['jquery_path'] = 'admin/components/ckeditor/adapters/jquery.js';
                $fieldOptions['config'] = $this->getCKEditroDefaultConfig($builderOptions);
                $fieldOptions['plugins'] = $this->getCKEditorDefaultPlugins();
            break;

        }

        // dump([$name, $this->currentType]);

        return $fieldOptions;
    }


    protected function getOptionsAnimations(array $builderOptions = array())
    {

        $options = parent::getOptionsAnimations($builderOptions);

        $options['entry_type'] = 'Parabol\\AdminCoreBundle\\Form\\Type\\Animation\\NewType';
        $options['entry_options'] = [];
        return $options;
        
        // $optionsClass = 'Parabol\AdminCoreBundle\Form\Type\Page\Options';
        // $options = class_exists($optionsClass) ? new $optionsClass() : null;

        // return $this->resolveOptions('animations', array(  'label' => 'Animations',  'translation_domain' => 'Admingenerator',  'allow_add' => true,  'allow_delete' => true,  'by_reference' => false,  'entry_type' => 'Parabol\\AdminCoreBundle\\Form\\Type\\Animation\\NewType',  'entry_options' =>   array(      ),), $builderOptions, $options);

        // 'class' => 'Parabol\\AdminCoreBundle\\Entity\\Animation',
    }

    protected function getOptionsContainerStyles(array $builderOptions = array())
    {
        $options = parent::getOptionsContainerStyles($builderOptions);

        $options['entry_type'] = 'Parabol\\AdminCoreBundle\\Form\\Type\\ContainerStyle\\NewType';
        $options['entry_options'] = [];
        return $options;


        // $optionsClass = 'Parabol\AdminCoreBundle\Form\Type\Page\Options';
        // $options = class_exists($optionsClass) ? new $optionsClass() : null;

        // return $this->resolveOptions('animations', array(  'label' => 'Animations',  'translation_domain' => 'Admingenerator',  'allow_add' => true,  'allow_delete' => true,  'by_reference' => false,  'entry_type' => 'Parabol\\AdminCoreBundle\\Form\\Type\\ContainerStyle\\NewType',  'entry_options' =>   array(      ),), $builderOptions, $options);

        // 'class' => 'Parabol\\AdminCoreBundle\\Entity\\Animation',
    }

// protected function getOptionsFiles(array $builderOptions = array())
//     {
//         if(method_exists(get_parent_class(), 'getOptionsFiles')) $result = parent::getOptionsFiles($builderOptions);
//         else $result = array();
        
//         unset($result['allow_add'], $result['allow_delete'], $result['type'], $result['options']);

//         $class = $this->builder->getDataClass();
//         if($class::allowMultipleFiles())
//         {
//             $result['attr']['multiple'] = true;
//         } 

//         if(!isset($result['attr']['labels'])) $result['attr']['labels'] = [];
//         $result['attr']['data-class'] = $class;
//         $result['attr']['data-ref'] = $this->builder->getData()->getId();



//         if($result['label'] == 'Files') $result['label'] = ' ';

//         $result = $this->optionsFixer($result);

//         return $result;
//     }

}