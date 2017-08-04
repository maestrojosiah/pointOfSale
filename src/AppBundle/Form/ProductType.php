<?php 

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$product = $options['data'];
		$user = $client->getUser();		
		$builder
			->add('product_code', TextType::class, array('label' => 'Product Title') )
			->add('product_name', TextType::class, array('label' => 'Product Name') )
			->add('product_cost', MoneyType::class, array('label' => 'Product Cost') )
			->add('product_tax', NumberType::class, array('label' => 'Tax') )
			->add('product_retail_price', MoneyType::class, array('label' => 'Product Icon (PNG / JPG', 'data_class' => null) )
			->add('job', EntityType::class, array(
				'label' => 'Choose Category',
				'class' => 'AppBundle:Category',
				// 'choice_label' => 'title'
				'choices' => $user->getProducts()
			))
			->add('save', SubmitType::class, array('label' => 'Save Product'))
            ->add('saveAndAdd', SubmitType::class, array('label' => 'Save and Add Client'))
			->getForm();
		;
	}

	public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product',
        ));
    }

}