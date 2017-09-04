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
	 * @Route("sale/view/{saleId}", name="sale_view")
	 */
	public function viewAction(Request $request, $saleId)
	{
		$data = [];
		$sale = $this->getDoctrine()
			->getRepository('AppBundle:Sale')
			->find($saleId);

		$stocks = $this->getDoctrine()
			->getRepository('AppBundle:Stock')
			->findAllForThisSale($sale);
		$data['sale'] = $sale;
		$data['stocks'] = $stocks;

		return $this->render('sale/view.html.twig', ['data' => $data,] );

	}


	/**
	 * @Route("sale/complete/{saleId}", name="sale_view_complete")
	 */
	public function completeAction(Request $request, $saleId)
	{
		$data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
		$sale = $this->getDoctrine()
			->getRepository('AppBundle:Sale')
			->find($saleId);

		$stocks = $this->getDoctrine()
			->getRepository('AppBundle:Stock')
			->findAllForThisSale($sale);

        $products = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->loadAllProductsFromThisUser($user);

        $categories = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->loadAllCategoriesFromThisUser($user);

        $systSetting = $this->getDoctrine()
            ->getRepository('AppBundle:SystSetting')
            ->settingsForThisUser($user);

		$data['sale'] = $sale;
		$data['stocks'] = $stocks;
		$data['categories'] = $categories;
		$data['products'] = $products;
		$data['systSetting'] = $systSetting;

		return $this->render('sale/complete.html.twig', ['data' => $data,] );

	}


	/**
	 * @Route("sale/sus", name="suspended_view")
	 */
	public function suspendedAction(Request $request)
	{
		$data = [];
		$sales = $this->getDoctrine()
			->getRepository('AppBundle:Sale')
			->findAllSuspended();

		$data['sales'] = $sales;

		return $this->render('sale/sus.html.twig', ['data' => $data,] );

	}



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
		$stocks = $sale->getStocks();
		foreach($stocks as $stock){
			$stock->setDeleted(true);
		}

		$sale->setDeleted(true);
 
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

		$stocks = $sale->getStocks();
		foreach($stocks as $stock){
			$stock->setDeleted(false);
		}

		$sale->setDeleted(false);

        	$this->addFlash(
	            'success',
	            'Sale restored successfully!'
        	);

		$em->persist($sale);
		$em->flush(); 

		return $this->redirectToRoute('restore_list');
	}

	/**
	 * @Route("/sale/lastSale", name="last_sale")
	 */
	public function lastReceiptAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $systSetting = $this->getDoctrine()
            ->getRepository('AppBundle:SystSetting')
            ->settingsForThisUser($user);

	    $em = $this->getDoctrine()->getManager();
        $lastSale = $em->getRepository('AppBundle:Sale')
        	->loadLastSaleEntry();
    	$data['systSetting'] = $systSetting;
    	$data['lastSale'] = $lastSale;
		return $this->render('sale/lastSale.html.twig', ['data' => $data ] );
	}

}