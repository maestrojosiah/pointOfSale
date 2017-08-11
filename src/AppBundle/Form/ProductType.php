<?php 

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class ProductType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$product = $options['data'];
		$user = $product->getUser();		
		$builder
			->add('product_code', TextType::class, array('label' => 'Product Code') )
			->add('product_name', TextType::class, array('label' => 'Product Name') )
			->add('product_cost', MoneyType::class, array('label' => 'Product Cost') )
			->add('product_retail_price', MoneyType::class, array('label' => 'Product Retail Price' ) )
			->add('product_tax', ChoiceType::class, array(
				'choices' => array(
					'No Tax' => 0,
					'16%' => 1.16,
					),
				'label' => 'Tax'
				)
			)			
			->add('category', EntityType::class, array(
				'label' => 'Choose Category',
				'class' => 'AppBundle:Category',
				// 'query_builder' => function (EntityRepository $er, $user) {
			 //        return $er->createQueryBuilder('c')
			 //        	->where('c.user = :user AND c.deleted = 0')
			 //        	->setParameter('user', $user)
			 //            ->orderBy('c.id', 'ASC');
			 //    },
    			// 'choice_label' => 'title'
				'choices' => $user->getCategories()
			))
			->add('save', SubmitType::class, array('label' => 'Save Product'))
            ->add('saveAndAdd', SubmitType::class, array('label' => 'Save and Add Product'))
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