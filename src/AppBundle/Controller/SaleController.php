<?php 

namespace AppBundle\Controller;

use AppBundle\Form\SaleType;
use AppBundle\Entity\Sale;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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
	 * @Route("sale/purchase_view/{saleId}", name="purchase_view")
	 */
	public function viewPurchaseAction(Request $request, $saleId)
	{
		$data = [];
		$sale = $this->getDoctrine()
			->getRepository('AppBundle:Sale')
			->find($saleId);
		$em = $this->getDoctrine()->getManager();

		$stocks = $em->getRepository('AppBundle:Stock')
			->findAllForThisSale($sale);
		$data['sale'] = $sale;
		$data['stocks'] = $stocks;

        $refNoArray = explode("|", $sale->getPaymentMode());
        $refNo = isset($refNoArray[1]) ? $refNoArray[1] : 'Not Set' ;
        
        $details = explode('|', $sale->getDetails());
        $company = $details[0] != "" ? $details[0] : 'No Company Set' ;
        $address = isset($details[1]) ? $details[1] : 'No Address Set' ;

        $lastSaleId = $em->getRepository('AppBundle:Sale')
        	->loadLastSaleEntry();
        $nextSaleId = $lastSaleId->getId() + 1;

        $data['refNo'] = $refNo;
        $data['address'] = $address;
        $data['company'] = $company;
        $data['nextSaleId'] = $nextSaleId;
		return $this->render('sale/purchase.html.twig', ['data' => $data,] );

	}

	/**
	 * @Route("sale/purchases_view/{download}", name="purchases_view")
	 */
	public function viewPurchasesAction(Request $request, $download = "False")
	{
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data['specificSale'] = [];
        $data['rangeSale'] = [];
        $data['dates']['specific'] = '';
        $data['dates']['from'] = '';
        $data['dates']['to'] = '';
        $form = $this   ->createFormBuilder()
                        ->add('dateSpecific')
                        ->add('dateFrom')
                        ->add('dateTo')
                        ->getForm();
     
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $form_data = $form->getData();

            $data['dates']['specific'] = $form_data['dateSpecific'];
            $data['dates']['from'] = $form_data['dateFrom'];
            $data['dates']['to'] = $form_data['dateTo'];

            $em = $this->getDoctrine()->getManager();
            if($data['dates']['specific']){
                $startDay = new \DateTime($data['dates']['specific']);
                $startDay ->setTime(0, 0, 0);

                $endDay = clone $startDay;
                $endDay->modify('+1 day'); 
                $sales = $em->getRepository('AppBundle:Sale')
                    ->findAllForThisDate($startDay, $endDay, "Purchase");
                $data['specificSale'] = $sales;
            } 

            if ($data['dates']['from']) {
                $from = new \DateTime($data['dates']['from']);
                $to = new \DateTime($data['dates']['to']);
                $from ->setTime(0, 0, 0);
                $to ->setTime(0, 0, 0);
                $to -> modify('+1 day');
                $sales = $em->getRepository('AppBundle:Sale')
                    ->findAllForThisRange($from, $to, "Purchase");
                $data['rangeSale'] = $sales;
            }
            
            if($download == "True" ){
                $entity = $sales;

                foreach($entity as $entry){
                    $dataInfo['date'] = $entry->getOnDate();
                    $dataInfo['number'] = explode("|", ($entry->getPaymentMode()))[2];
                    $dataInfo['refId'] = explode("|", ($entry->getPaymentMode()))[1];
                    $dataInfo['details'] = 'Purchase Invoice';
                    $dataInfo['amount'] = $entry->getTotalSale();
                    $dataInfo['no_dates'] = true;
                    $dataArray[] = $dataInfo;
                }

                $systSetting = $em->getRepository('AppBundle:systSetting')
                    ->findOneByUser($user);
                    
                $data['systSetting'] = $systSetting;
                $data['report'] = "Purchases Report";


                $data['entity'] = $dataArray;
                $data['property'] = $download;

                $appPath = $this->container->getParameter('kernel.root_dir');

                $html = $this->renderView('PDF/pdf.html.twig', $data);

                $filename = sprintf("purchases-%s.pdf", date('Ymd~his'));

                return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                    200,
                    [
                        'Content-Type'        => 'application/pdf',
                        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                    ]
                );
            }

        }

        return $this->render('sale/purchases.html.twig', $data );

	}

	/**
	 * @Route("sale/return_view/{saleId}", name="return_view")
	 */
	public function viewReturnAction(Request $request, $saleId)
	{
		$data = [];
		$sale = $this->getDoctrine()
			->getRepository('AppBundle:Sale')
			->find($saleId);

		$em = $this->getDoctrine()->getManager();

		$stocks = $em->getRepository('AppBundle:Stock')
			->findAllForThisSale($sale);

		$data['sale'] = $sale;
		$data['stocks'] = $stocks;

        $refNoArray = explode("|", $sale->getPaymentMode());
        $refNo = isset($refNoArray[1]) ? $refNoArray[1] : 'Not Set' ;
        
        $details = explode('|', $sale->getDetails());
        $company = $details[0] != "" ? $details[0] : 'No Company Set' ;
        $address = isset($details[1]) ? $details[1] : 'No Address Set' ;

        $lastSaleId = $em->getRepository('AppBundle:Sale')
        	->loadLastSaleEntry();
        $nextSaleId = $lastSaleId->getId() + 1;

        $data['refNo'] = $refNo;
        $data['address'] = $address;
        $data['company'] = $company;
        $data['nextSaleId'] = $nextSaleId;

		return $this->render('sale/return.html.twig', ['data' => $data,] );

	}

	/**
	 * @Route("sale/returns_view/{download}", name="returns_view")
	 */
	public function viewReturnsAction(Request $request, $download = "False")
	{
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data['specificSale'] = [];
        $data['rangeSale'] = [];
        $data['dates']['specific'] = '';
        $data['dates']['from'] = '';
        $data['dates']['to'] = '';
        $form = $this   ->createFormBuilder()
                        ->add('dateSpecific')
                        ->add('dateFrom')
                        ->add('dateTo')
                        ->getForm();
     
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $form_data = $form->getData();

            $data['dates']['specific'] = $form_data['dateSpecific'];
            $data['dates']['from'] = $form_data['dateFrom'];
            $data['dates']['to'] = $form_data['dateTo'];

            $em = $this->getDoctrine()->getManager();
            if($data['dates']['specific']){
                $startDay = new \DateTime($data['dates']['specific']);
                $startDay ->setTime(0, 0, 0);

                $endDay = clone $startDay;
                $endDay->modify('+1 day'); 
                $sales = $em->getRepository('AppBundle:Sale')
                    ->findAllForThisDate($startDay, $endDay, "Return");
                $data['specificSale'] = $sales;
            } 

            if ($data['dates']['from']) {
                $from = new \DateTime($data['dates']['from']);
                $to = new \DateTime($data['dates']['to']);
                $from ->setTime(0, 0, 0);
                $to ->setTime(0, 0, 0);
                $to -> modify('+1 day');
                $sales = $em->getRepository('AppBundle:Sale')
                    ->findAllForThisRange($from, $to, "Return");
                $data['rangeSale'] = $sales;
            }
            
            if($download == "True" ){
                $entity = $sales;

                foreach($entity as $entry){
                    $dataInfo['date'] = $entry->getOnDate();
                    $dataInfo['number'] = explode("|", ($entry->getPaymentMode()))[2];
                    $dataInfo['refId'] = explode("|", ($entry->getPaymentMode()))[1];
                    $dataInfo['details'] = 'Debit Note';
                    $dataInfo['amount'] = $entry->getTotalSale();
                    $dataInfo['no_dates'] = true;
                    $dataArray[] = $dataInfo;
                }

                $systSetting = $em->getRepository('AppBundle:systSetting')
                    ->findOneByUser($user);
                    
                $data['systSetting'] = $systSetting;
                $data['report'] = "Purchases Return Report";


                $data['entity'] = $dataArray;
                $data['property'] = $download;

                $appPath = $this->container->getParameter('kernel.root_dir');

                $html = $this->renderView('PDF/pdf.html.twig', $data);

                $filename = sprintf("received-%s.pdf", date('Ymd~his'));

                return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                    200,
                    [
                        'Content-Type'        => 'application/pdf',
                        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                    ]
                );
            }

        }

        return $this->render('sale/returns.html.twig', $data );

	}

	/**
	 * @Route("sale/mpesa/{download}", name="mpesa_view")
	 */
	public function viewMpesaAction(Request $request, $download = "False")
	{
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data['specificSale'] = [];
        $data['rangeSale'] = [];
        $data['dates']['specific'] = '';
        $data['dates']['from'] = '';
        $data['dates']['to'] = '';
        $form = $this   ->createFormBuilder()
                        ->add('dateSpecific')
                        ->add('dateFrom')
                        ->add('dateTo')
                        ->getForm();
     
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $form_data = $form->getData();

            $data['dates']['specific'] = $form_data['dateSpecific'];
            $data['dates']['from'] = $form_data['dateFrom'];
            $data['dates']['to'] = $form_data['dateTo'];

            $em = $this->getDoctrine()->getManager();
            if($data['dates']['specific']){
                $startDay = new \DateTime($data['dates']['specific']);
                $startDay ->setTime(0, 0, 0);

                $endDay = clone $startDay;
                $endDay->modify('+1 day'); 
                $sales = $em->getRepository('AppBundle:Sale')
                    ->findAllForThisDate($startDay, $endDay, "mpesa");
                $data['specificSale'] = $sales;
            } 

            if ($data['dates']['from']) {
                $from = new \DateTime($data['dates']['from']);
                $to = new \DateTime($data['dates']['to']);
                $from ->setTime(0, 0, 0);
                $to ->setTime(0, 0, 0);
                $to -> modify('+1 day');
                $sales = $em->getRepository('AppBundle:Sale')
                    ->findAllForThisRange($from, $to, "mpesa");
                $data['rangeSale'] = $sales;
            }
            
            if($download == "True" ){
                $entity = $sales;

                foreach($entity as $entry){
                    $dataInfo['date'] = $entry->getOnDate();
                    $dataInfo['transaction'] = explode("|", ($entry->getPaymentMode()))[1];
                    $dataInfo['amount'] = $entry->getTotalSale();
                    $dataArray[] = $dataInfo;
                }

                $systSetting = $em->getRepository('AppBundle:systSetting')
                    ->findOneByUser($user);
                    
                $data['systSetting'] = $systSetting;
                $data['report'] = "Mpesa Report";


                $data['entity'] = $dataArray;
                $data['property'] = $download;

                $appPath = $this->container->getParameter('kernel.root_dir');

                $html = $this->renderView('PDF/pdf.html.twig', $data);

                $filename = sprintf("purchases-%s.pdf", date('Ymd~his'));

                return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                    200,
                    [
                        'Content-Type'        => 'application/pdf',
                        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                    ]
                );
            }

        }

        return $this->render('sale/mpesa.html.twig', $data );

	}

	/**
	 * @Route("sale/creditCard/{download}", name="creditCard_view")
	 */
	public function viewCCardAction(Request $request, $download = "False")
	{
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data['specificSale'] = [];
        $data['rangeSale'] = [];
        $data['dates']['specific'] = '';
        $data['dates']['from'] = '';
        $data['dates']['to'] = '';
        $form = $this   ->createFormBuilder()
                        ->add('dateSpecific')
                        ->add('dateFrom')
                        ->add('dateTo')
                        ->getForm();
     
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $form_data = $form->getData();

            $data['dates']['specific'] = $form_data['dateSpecific'];
            $data['dates']['from'] = $form_data['dateFrom'];
            $data['dates']['to'] = $form_data['dateTo'];

            $em = $this->getDoctrine()->getManager();
            if($data['dates']['specific']){
                $startDay = new \DateTime($data['dates']['specific']);
                $startDay ->setTime(0, 0, 0);

                $endDay = clone $startDay;
                $endDay->modify('+1 day'); 
                $sales = $em->getRepository('AppBundle:Sale')
                    ->findAllForThisDate($startDay, $endDay, "creditCard");
                $data['specificSale'] = $sales;
            } 

            if ($data['dates']['from']) {
                $from = new \DateTime($data['dates']['from']);
                $to = new \DateTime($data['dates']['to']);
                $from ->setTime(0, 0, 0);
                $to ->setTime(0, 0, 0);
                $to -> modify('+1 day');
                $sales = $em->getRepository('AppBundle:Sale')
                    ->findAllForThisRange($from, $to, "creditCard");
                $data['rangeSale'] = $sales;
            }
            
            if($download == "True" ){
                $entity = $sales;

                foreach($entity as $entry){
                    $dataInfo['date'] = $entry->getOnDate();
                    $dataInfo['transaction'] = explode("|", ($entry->getPaymentMode()))[1];
                    $dataInfo['amount'] = $entry->getTotalSale();
                    $dataArray[] = $dataInfo;
                }

                $systSetting = $em->getRepository('AppBundle:systSetting')
                    ->findOneByUser($user);
                    
                $data['systSetting'] = $systSetting;
                $data['report'] = "Credit Card Report";


                $data['entity'] = $dataArray;
                $data['property'] = $download;

                $appPath = $this->container->getParameter('kernel.root_dir');

                $html = $this->renderView('PDF/pdf.html.twig', $data);

                $filename = sprintf("purchases-%s.pdf", date('Ymd~his'));

                return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                    200,
                    [
                        'Content-Type'        => 'application/pdf',
                        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                    ]
                );
            }

        }

        return $this->render('sale/creditCard.html.twig', $data );

	}

	/**
	 * @Route("sale/cheque/{download}", name="cheque_view")
	 */
	public function viewChequeAction(Request $request, $download = "False")
	{
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data['specificSale'] = [];
        $data['rangeSale'] = [];
        $data['dates']['specific'] = '';
        $data['dates']['from'] = '';
        $data['dates']['to'] = '';
        $form = $this   ->createFormBuilder()
                        ->add('dateSpecific')
                        ->add('dateFrom')
                        ->add('dateTo')
                        ->getForm();
     
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $form_data = $form->getData();

            $data['dates']['specific'] = $form_data['dateSpecific'];
            $data['dates']['from'] = $form_data['dateFrom'];
            $data['dates']['to'] = $form_data['dateTo'];

            $em = $this->getDoctrine()->getManager();
            if($data['dates']['specific']){
                $startDay = new \DateTime($data['dates']['specific']);
                $startDay ->setTime(0, 0, 0);

                $endDay = clone $startDay;
                $endDay->modify('+1 day'); 
                $sales = $em->getRepository('AppBundle:Sale')
                    ->findAllForThisDate($startDay, $endDay, "check");
                $data['specificSale'] = $sales;
            } 

            if ($data['dates']['from']) {
                $from = new \DateTime($data['dates']['from']);
                $to = new \DateTime($data['dates']['to']);
                $from ->setTime(0, 0, 0);
                $to ->setTime(0, 0, 0);
                $to -> modify('+1 day');
                $sales = $em->getRepository('AppBundle:Sale')
                    ->findAllForThisRange($from, $to, "check");
                $data['rangeSale'] = $sales;
            }
            
            if($download == "True" ){
                $entity = $sales;

                foreach($entity as $entry){
                    $dataInfo['date'] = $entry->getOnDate();
                    $dataInfo['transaction'] = explode("|", ($entry->getPaymentMode()))[1];
                    $dataInfo['amount'] = $entry->getTotalSale();
                    $dataArray[] = $dataInfo;
                }

                $systSetting = $em->getRepository('AppBundle:systSetting')
                    ->findOneByUser($user);
                    
                $data['systSetting'] = $systSetting;
                $data['report'] = "Cheque Report";


                $data['entity'] = $dataArray;
                $data['property'] = $download;

                $appPath = $this->container->getParameter('kernel.root_dir');

                $html = $this->renderView('PDF/pdf.html.twig', $data);

                $filename = sprintf("purchases-%s.pdf", date('Ymd~his'));

                return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                    200,
                    [
                        'Content-Type'        => 'application/pdf',
                        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                    ]
                );
            }

        }

        return $this->render('sale/cheque.html.twig', $data );

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

        $categories = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->loadAllCategoriesFromThisUser($user);

        $systSetting = $this->getDoctrine()
            ->getRepository('AppBundle:SystSetting')
            ->settingsForThisUser($user);

		$data['sale'] = $sale;
		$data['stocks'] = $stocks;
		$data['categories'] = $categories;
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
	 * @Route("/sale/suspended/delete/{saleId}", name="sale_suspended_delete")
	 */
	public function deleteSusAction($saleId)
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

		return $this->redirectToRoute('suspended_view');
	}

	/**
	 * @Route("/sale/ajax/delete", name="delete_sale_ajax")
	 */
	public function deleteAjaxAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$saleId = $request->request->get('id');
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
	            'Suspended sale completed successfully!'
        	);


		$em->persist($sale);
		$em->flush(); 

        return new JsonResponse($saleId);
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

	/**
	 * @Route("/sale/pdf/save/{category}/{saleId}", name="save_pdf_sale")
	 */
	public function printPDFAction(Request $request, $category, $saleId){

		$data = [];
		$sale = $this->getDoctrine()
			->getRepository('AppBundle:Sale')
			->find($saleId);

		$em = $this->getDoctrine()->getManager();

		$stocks = $em->getRepository('AppBundle:Stock')
			->findAllForThisSale($sale);

		$data['sale'] = $sale;
		$data['stocks'] = $stocks;

        $refNoArray = explode("|", $sale->getPaymentMode());
        $refNo = isset($refNoArray[1]) ? $refNoArray[1] : 'Not Set' ;
        
        $details = explode('|', $sale->getDetails());
        $company = $details[0] != "" ? $details[0] : 'No Company Set' ;
        $address = isset($details[1]) ? $details[1] : 'No Address Set' ;

        $lastSaleId = $em->getRepository('AppBundle:Sale')
        	->loadLastSaleEntry();
        $nextSaleId = $lastSaleId->getId() + 1;

        $data['refNo'] = $refNo;
        $data['address'] = $address;
        $data['company'] = $company;
        $data['nextSaleId'] = $nextSaleId;


        $appPath = $this->container->getParameter('kernel.root_dir');

        $html = $this->renderView('PDF/'.$category.'.html.twig', ['data'=>$data]);

        $filename = sprintf("$category-%s.pdf", date('Ymd~his'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );

	}

}