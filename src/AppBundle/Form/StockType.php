<?php 

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class StockType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$stock = $options['data'];
		$user = $stock->getUser();		
		$builder
			->add('quantity', TextType::class, array('label' => 'Quantity') )
			->add('transaction', ChoiceType::class, array(
				'choices' => array(
					 'Stock in' 	=> 'sto',
					 'Sale' 		=> 'sal',
					 'Sale Return' 	=> 'ret',
					),
				'label' => 'Transaction'
				)
			)			
			->add('save', SubmitType::class, array('label' => 'Save Stock'))
			->getForm();
		;
	}

	public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Stock',
        ));
    }

}