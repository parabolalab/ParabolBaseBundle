<?php
namespace Parabol\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DateRangeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
         $resolver->setDefaults(array(
            'widgets_options' => []
         ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $widgteOptions = [
            // 'widget' => $options['widget'],
            // 'format' => $options['format'],
            // 'attr' => $options['attr']
        ];

        // unset($options['children']);

        $builder
          ->add('from', DateType::class, $options['widgets_options'])
          ->add('to', DateType::class, $options['widgets_options']);
    }
}