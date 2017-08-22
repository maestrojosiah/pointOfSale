<?php 

namespace AppBundle\Controller;

use AppBundle\Form\SystSettingType;
use AppBundle\Entity\SystSetting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;

class SystSettingController extends Controller
{
	
	/**
	 * @Route("/systSetting/add", name="systSetting_add")
	 */
	public function addAction(Request $request) 
	{
		$data = [];
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $data['user'] = $user;

        $em = $this->getDoctrine()->getManager();

        $thisUser = $em->getRepository('AppBundle:SystSetting')
        	->settingsForThisUser($user);

        if($thisUser){
        	$systSetting = $thisUser;
        } else {
        	$systSetting = new SystSetting();
        }

        $systSetting->setUser($user);
        $form = $this->createForm(SystSettingType::class, $systSetting);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

			$last_entity = $em->getRepository('AppBundle:SystSetting')
				->loadLastSystSettingEntry();

            $systSetting->setUser($user);

	            if(!$last_entity){
	            	$systSetting->setOrigId(1);
	            } else {
	            	$lastInputId = $last_entity->getId();
	            	$thisId = $lastInputId + 1;
	            	$systSetting->setOrigId($thisId);
	        	}

            $em->persist($systSetting);
            $em->flush();

        	$this->addFlash(
	            'success',
	            'Settings added successfully!'
        	);

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('homepage');
		} else {
			$form_data['site_name'] = $systSetting->getSiteName();
			$form_data['telephone'] = $systSetting->getTelephone();
			$form_data['currency_code'] = $systSetting->getCurrencyCode();
			$form_data['rows_per_page'] = $systSetting->getRowsPerPage();
			$form_data['receipt_header'] = $systSetting->getReceiptHeader();
			$form_data['receipt_footer'] = $systSetting->getReceiptFooter();
			$form_data['min_stock'] = $systSetting->getMinStock();
			$form_data['upload_duration'] = $systSetting->getUploadDuration();
			$data['form'] = $form_data;
		}


        return $this->render(
            'systSetting/add.html.twig',
            array('form' => $form->createView())
        );

	}


}