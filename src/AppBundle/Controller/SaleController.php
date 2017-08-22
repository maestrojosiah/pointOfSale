<?php 

namespace AppBundle\Controller;

use AppBundle\Form\SaleType;
use AppBundle\Entity\Sale;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SaleController extends Controller
{

	/**
	 * @Route("/sale/edit/{saleId}", name="sale_edit")
	 */
	public function editAction(Request $request, $saleId)
	{
		$data = [];
		$sale = $this->getDoctrine()
			->getRepository('AppBundle:Sale')
			->find($saleId);

		$form = $this->createForm(SaleType::class, $sale);

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$form_data = $form->getData();
			$data['form'] = $form_data;
			$em = $this->getDoctrine()->getManager();
			$em->persist($form_data);
			$em->flush();

        	$this->addFlash(
	            'success',
	            'Sale edited successfully!'
        	);

			return $this->redirectToRoute('sale_list');
		} else {
			$form_data['sale_name'] = $sale->getSaleName();
			$data['form'] = $form_data;
		}

		return $this->render('sale/edit.html.twig', ['form' => $form->createView(), 'data' => $data,] );

	}

	/**
	 * @Route("/sale/list", name="sale_list")
	 */
	public function listAction()
	{
		$data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
		$sales = $this->getDoctrine()
			->getRepository('AppBundle:Sale')
			->loadAllSalesFromThisUser($user);

		$data['sales'] = $sales;

		return $this->render('sale/list.html.twig', ['data' => $data ] );

	}

	/**
	 * @Route("/sale/restore", name="restore_list")
	 */	
	public function restoreListAction()
	{
		$data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
		$sales = $this->getDoctrine()
			->getRepository('AppBundle:Sale')
			->loadAllDeletedSalesFromThisUser($user);

		$data['sales'] = $sales;

		return $this->render('sale/restore.html.twig', ['data' => $data ] );

	}

	/**
	 * @Route("/sale/delete/{saleId}", name="sale_delete")
	 */
	public function deleteAction($saleId)
	{
		$em = $this->getDoctrine()->getManager();

		$sale = $em->getRepository('AppBundle:Sale')
			->find($saleId);

		if(!$sale) {
			throw $this->createNotFoundException(
				'No sale for this id'
			);
		}

		$sale->setDeleted(1);
 
        	$this->addFlash(
	            'success',
	            'Sale deleted successfully!'
        	);


		$em->persist($sale);
		$em->flush(); 

		return $this->redirectToRoute('sale_list');
	}

	/**
	 * @Route("/sale/restore/{saleId}", name="sale_restore")
	 */
	public function restoreAction($saleId)
	{
		$em = $this->getDoctrine()->getManager();

		$sale = $em->getRepository('AppBundle:Sale')
			->find($saleId);

		if(!$sale) {
			throw $this->createNotFoundException(
				'No sale for this id'
			);
		}

		$sale->setDeleted(0);

        	$this->addFlash(
	            'success',
	            'Sale restored successfully!'
        	);

		$em->persist($sale);
		$em->flush(); 

		return $this->redirectToRoute('restore_list');
	}

}