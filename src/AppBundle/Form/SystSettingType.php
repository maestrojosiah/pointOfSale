<?php

namespace AppBundle\Form;

use AppBundle\Entity\SystSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class SystSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site_name', TextType::class)
            ->add('telephone', TextType::class)
            ->add('currency_code', ChoiceType::class, array(
				    'choices' => array(
				        'Kenya Shillings' => 'Kshs',
				        'US Dollars' => 'USD',
				    ),
				    'preferred_choices' => array('Kshs', 'arr')
				))
            ->add('rows_per_page', ChoiceType::class, array(
				    'choices' => array(
				        '10 Rows' => 10,
				        '25 Rows' => 25,
				        '50 Rows' => 50,
				        '100 Rows' => 100,
				    ),
				    'preferred_choices' => array('20', 'arr')
				))
            ->add('upload_duration', ChoiceType::class, array(
				    'choices' => array(
				        '10 Seconds' => 10,
				        '20 Seconds' => 20,
				        '30 Seconds' => 30,
				        '1 Minute' => 60,
				        '2 Minutes' => 120,
				        '5 Minutes' => 300,
				    ),
				    'preferred_choices' => array('300', 'arr')
				))
            ->add('receipt_header', CKEditorType::class)
            ->add('receipt_footer', CKEditorType::class)
            ->add('min_stock', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Save Settings'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SystSetting::class,
        ));
    }
}