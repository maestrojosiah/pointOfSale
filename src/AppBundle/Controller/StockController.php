<?php 

namespace AppBundle\Controller;

use AppBundle\Entity\Stock;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;

class StockController extends Controller
{

    /**
     * @Route("/stock/received/{download}", name="items_received")
     */
    public function receivedAction(Request $request, $download = "False")
    {
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data['specificStock'] = [];
        $data['rangeStock'] = [];
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
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->findAllForThisDate($startDay, $endDay, "sto");
                $data['specificStock'] = $stocks;
            } 

            if ($data['dates']['from']) {
                $from = new \DateTime($data['dates']['from']);
                $to = new \DateTime($data['dates']['to']);
                $from ->setTime(0, 0, 0);
                $to ->setTime(0, 0, 0);
                $to -> modify('+1 day');
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->findAllForThisRange($from, $to, "sto");
                $data['rangeStock'] = $stocks;
            }
            
            if($download == "True" ){
                $entity = $stocks;

                foreach($entity as $entry){
                    $dataInfo['name'] = $entry->getProduct();
                    $dataInfo['date'] = $entry->getOnDate();
                    $dataInfo['quantity'] = $entry->getQuantity();
                    $dataInfo['cost'] = $entry->getRetailCost();
                    $dataArray[] = $dataInfo;
                }

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

        return $this->render('stock/received.html.twig', $data );

    }

    /**
     * @Route("/stock/returned/{download}", name="items_returned")
     */
    public function returnedAction(Request $request, $download = "False" )
    {
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data['specificStock'] = [];
        $data['rangeStock'] = [];
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
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->findAllForThisDate($startDay, $endDay, "ret");
                $data['specificStock'] = $stocks;
            } 

            if ($data['dates']['from']) {
                $from = new \DateTime($data['dates']['from']);
                $to = new \DateTime($data['dates']['to']);
                $from ->setTime(0, 0, 0);
                $to ->setTime(0, 0, 0);
                $to -> modify('+1 day');
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->findAllForThisRange($from, $to, "ret");
                $data['rangeStock'] = $stocks;
            }

            if($download == "True" ){
                $entity = $stocks;

                foreach($entity as $entry){
                    $dataInfo['name'] = $entry->getProduct();
                    $dataInfo['date'] = $entry->getOnDate();
                    $dataInfo['quantity'] = $entry->getQuantity();
                    $dataInfo['cost'] = $entry->getRetailCost();
                    $dataArray[] = $dataInfo;
                }

                $data['entity'] = $dataArray;
                $data['property'] = $download;

                $appPath = $this->container->getParameter('kernel.root_dir');

                $html = $this->renderView('PDF/pdf.html.twig', $data);

                $filename = sprintf("returns-%s.pdf", date('Ymd~his'));

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

        return $this->render('stock/returns.html.twig', $data );

    }

    /**
     * @Route("/stock/movement/{download}", name="stock_movement")
     */
    public function stockAction(Request $request, $download = "False")
    {
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data['specificStock'] = [];
        $data['rangeStock'] = [];
        $data['dates']['specific'] = '';
        $data['dates']['from'] = '';
        $data['dates']['to'] = '';
        $from = $data['dates']['from'];
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
                $from = new \DateTime($data['dates']['specific']);
                $from ->setTime(0, 0, 0);

                $to = clone $from;
                $to->modify('+1 day'); 
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->loadAllStocksFromThisUserWithRange($from, $to, $user);
                $data['specificStock'] = $stocks;
            } 

            if ($data['dates']['from']) {
                $from = new \DateTime($data['dates']['from']);
                $to = new \DateTime($data['dates']['to']);
                $from ->setTime(0, 0, 0);
                $to ->setTime(0, 0, 0);
                $to -> modify('+1 day');
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->loadAllStocksFromThisUserWithRange($from, $to, $user);
                $data['rangeStock'] = $stocks;
            } 
           

        } else {
        
            $stocks = $em->getRepository('AppBundle:Stock')
                ->loadAllStocksFromThisUser($user);
            $data['allStock'] = $stocks;

        }
            $returnsSoFar = $em->getRepository('AppBundle:Stock')
                ->calculateStockMovementBeforeThisDate($data['dates']['from'], "ret");
            $stockInSoFar = $em->getRepository('AppBundle:Stock')
                ->calculateStockMovementBeforeThisDate($data['dates']['from'], "sto");
            $salesSoFar = $em->getRepository('AppBundle:Stock')
                ->calculateStockMovementBeforeThisDate($data['dates']['from'], "sal");
            
            $data['returns'] = $returnsSoFar[0]['total'];
            $data['sales'] = $salesSoFar[0]['total'];
            $data['stockIn'] = $stockInSoFar[0]['total'];

            if($download == "True" ){

                if( $request->query->get('specific') ){
                    $data['allStock'] = null;
                    $from = new \DateTime($request->query->get('specific'));
                    $from ->setTime(0, 0, 0);

                    $to = clone $from;
                    $to->modify('+1 day'); 
                    $stocks = $em->getRepository('AppBundle:Stock')
                        ->loadAllStocksFromThisUserWithRange($from, $to, $user);
                    $data['specificStock'] = $stocks;
                } 

                if ( $request->query->get('from') ) {
                    $data['allStock'] = null;
                    $from = new \DateTime($request->query->get('from'));
                    $to = new \DateTime($request->query->get('to'));
                    $from ->setTime(0, 0, 0);
                    $to ->setTime(0, 0, 0);
                    $to -> modify('+1 day');
                    $stocks = $em->getRepository('AppBundle:Stock')
                        ->loadAllStocksFromThisUserWithRange($from, $to, $user);
                    $data['rangeStock'] = $stocks;
                } 

                $returnsSoFar = $em->getRepository('AppBundle:Stock')
                    ->calculateStockMovementBeforeThisDate($from, "ret");
                $stockInSoFar = $em->getRepository('AppBundle:Stock')
                    ->calculateStockMovementBeforeThisDate($from, "sto");
                $salesSoFar = $em->getRepository('AppBundle:Stock')
                    ->calculateStockMovementBeforeThisDate($from, "sal");
                
                $data['returns'] = $returnsSoFar[0]['total'];
                $data['sales'] = $salesSoFar[0]['total'];
                $data['stockIn'] = $stockInSoFar[0]['total'];


                $appPath = $this->container->getParameter('kernel.root_dir');

                $html = $this->renderView('PDF/stock_mov.html.twig', $data );

                $filename = sprintf("stockMovAll-%s.pdf", date('Ymd~his'));

                return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                    200,
                    [
                        'Content-Type'        => 'application/pdf',
                        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                    ]
                );
            }

        return $this->render('stock/stock.html.twig', $data );

    }

    /**
     * @Route("/stock/item/{id}{download}", name="stock_movement_item")
     */
    public function stockItemAction(Request $request, $id, $download = "False")
    {
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $product = $em->getRepository('AppBundle:product')
                ->find($id);
        $data['specificStock'] = [];
        $data['rangeStock'] = [];
        $data['dates']['specific'] = '';
        $data['dates']['from'] = '';
        $data['dates']['to'] = '';
        $from = $data['dates']['from'];
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
                $from = new \DateTime($data['dates']['specific']);
                $from ->setTime(0, 0, 0);
                $to = clone $from;
                $to->modify('+1 day'); 
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->loadAllStocksMovementForThisItem($from, $to, $user, $product);
                $data['specificStock'] = $stocks;
            } 
            if ($data['dates']['from']) {
                $from = new \DateTime($data['dates']['from']);
                $to = new \DateTime($data['dates']['to']);
                $from ->setTime(0, 0, 0);
                $to ->setTime(0, 0, 0);
                $to -> modify('+1 day');
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->loadAllStocksMovementForThisItem($from, $to, $user, $product);
                $data['rangeStock'] = $stocks;
            }           
        } else {
            $stocks = $em->getRepository('AppBundle:Stock')
                ->loadAllStocksFromThisItem($user, $product);
            $data['allStock'] = $stocks;
        }
        $returnsSoFar = $em->getRepository('AppBundle:Stock')
            ->calculateStockMovementBeforeThisDateItem($data['dates']['from'], "ret", $product);
        $stockInSoFar = $em->getRepository('AppBundle:Stock')
            ->calculateStockMovementBeforeThisDateItem($data['dates']['from'], "sto", $product);
        $salesSoFar = $em->getRepository('AppBundle:Stock')
            ->calculateStockMovementBeforeThisDateItem($data['dates']['from'], "sal", $product);
        
        $data['returns'] = $returnsSoFar[0]['total'];
        $data['sales'] = $salesSoFar[0]['total'];
        $data['stockIn'] = $stockInSoFar[0]['total'];

        if($download == "True" ){

            if( $request->query->get('specific') ){
                $data['allStock'] = null;
                $from = new \DateTime($data['dates']['specific']);
                $from ->setTime(0, 0, 0);
                $to = clone $from;
                $to->modify('+1 day'); 
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->loadAllStocksMovementForThisItem($from, $to, $user, $product);
                $data['specificStock'] = $stocks;
            } 

            if ( $request->query->get('from') ) {
                $data['allStock'] = null;
                $from = new \DateTime($data['dates']['from']);
                $to = new \DateTime($data['dates']['to']);
                $from ->setTime(0, 0, 0);
                $to ->setTime(0, 0, 0);
                $to -> modify('+1 day');
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->loadAllStocksMovementForThisItem($from, $to, $user, $product);
                $data['rangeStock'] = $stocks;
            } 

            $returnsSoFar = $em->getRepository('AppBundle:Stock')
                ->calculateStockMovementBeforeThisDateItem($from, "ret", $product);
            $stockInSoFar = $em->getRepository('AppBundle:Stock')
                ->calculateStockMovementBeforeThisDateItem($from, "sto", $product);
            $salesSoFar = $em->getRepository('AppBundle:Stock')
                ->calculateStockMovementBeforeThisDateItem($from, "sal", $product);
            
            $data['returns'] = $returnsSoFar[0]['total'];
            $data['sales'] = $salesSoFar[0]['total'];
            $data['stockIn'] = $stockInSoFar[0]['total'];


            $appPath = $this->container->getParameter('kernel.root_dir');

            $html = $this->renderView('PDF/stock_mov.html.twig', $data );

            $filename = sprintf("stockMov-%s.pdf", date('Ymd~his'));

            return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                ]
            );
        }

        return $this->render('stock/stockItem.html.twig', $data );
    }

    /**
     * @Route("/stock/sold/{download}", name="items_sold")
     */
    public function soldAction(Request $request, $download = "False")
    {
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data['specificStock'] = [];
        $data['rangeStock'] = [];
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
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->findAllForThisDate($startDay, $endDay, "sal");
                $data['specificStock'] = $stocks;
            } 

            if ($data['dates']['from']) {
                $from = new \DateTime($data['dates']['from']);
                $to = new \DateTime($data['dates']['to']);
                $from ->setTime(0, 0, 0);
                $to ->setTime(0, 0, 0);
                $to -> modify('+1 day');
                $stocks = $em->getRepository('AppBundle:Stock')
                    ->findAllForThisRange($from, $to, "sal");
                $data['rangeStock'] = $stocks;
            }

            if($download == "True" ){
                $entity = $stocks;

                foreach($entity as $entry){
                    $dataInfo['name'] = $entry->getProduct();
                    $dataInfo['date'] = $entry->getOnDate();
                    $dataInfo['quantity'] = $entry->getQuantity();
                    $dataInfo['cost'] = $entry->getRetailCost();
                    $dataArray[] = $dataInfo;
                }

                $data['entity'] = $dataArray;
                $data['property'] = $download;

                $appPath = $this->container->getParameter('kernel.root_dir');

                $html = $this->renderView('PDF/pdf.html.twig', $data);

                $filename = sprintf("sold-%s.pdf", date('Ymd~his'));

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

        return $this->render('stock/sold.html.twig', $data );

    }

	/**
	 * @Route("/stock/tax/{download}", name="tax_report")
	 */
	public function taxAction(Request $request, $download = "False")
	{
		$data = [];
		$em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data['specificStock'] = [];
        $data['rangeStock'] = [];
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
        		$stocks = $em->getRepository('AppBundle:Stock')
        			->findAllForThisDate($startDay, $endDay, "sal");
        		$data['specificStock'] = $stocks;
        	} 

        	if ($data['dates']['from']) {
        		$from = new \DateTime($data['dates']['from']);
        		$to = new \DateTime($data['dates']['to']);
        		$from ->setTime(0, 0, 0);
        		$to ->setTime(0, 0, 0);
        		$to -> modify('+1 day');
        		$stocks = $em->getRepository('AppBundle:Stock')
        			->findAllForThisRange($from, $to, "sal");
        		$data['rangeStock'] = $stocks;
        	}
        	
            if($download == "True" ){
                $entity = $stocks;

                foreach($entity as $entry){
                    $dataInfo['name'] = $entry->getProduct();
                    $dataInfo['date'] = $entry->getOnDate();
                    $dataInfo['quantity'] = $entry->getQuantity();
                    $dataInfo['tax'] = $entry->getRetailCost() * 16/116;
                    $dataArray[] = $dataInfo;
                }

                $data['entity'] = $dataArray;
                $data['property'] = $download;

                $appPath = $this->container->getParameter('kernel.root_dir');

                $html = $this->renderView('PDF/pdf.html.twig', $data);

                $filename = sprintf("sold-%s.pdf", date('Ymd~his'));

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

		return $this->render('stock/tax.html.twig', $data );

	}

	/**
	 * @Route("/stock/grouped/{download}", name="items_sold_grouped")
	 */
	public function soldGroupedAction(Request $request, $download = "False")
	{
		$data = [];
		$em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data['specificStock'] = [];
        $data['rangeStock'] = [];
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
        		$stocks = $em->getRepository('AppBundle:Stock')
        			->findAllForThisDate($startDay, $endDay, "sal");

                $arrayValues = [];
                foreach($stocks as $stock){
                    $arrayValues[]  = [$stock->getProduct()->getId(), $stock->getId()];
                }
                $vals = [];
                foreach($arrayValues as $val){
                    $vals[] = array_count_values($val);
                }
                
                $prod = [];
                $relatedArray = [];
                $relatedStock = [];
                foreach($vals as $key => $value){
                    $id = array_keys($value)[0];
                    $sId = array_keys($value)[1];
                    $relatedStock[] = [$id=>$sId];
                }

                $prodArray = [];

                $finale = array();
                array_walk_recursive($relatedStock, function($item, $key) use (&$finale){
                    $finale[$key] = isset($finale[$key]) ?  $item ."|". $finale[$key] : $item;
                });

                foreach($finale as $key => $value){
                    $ids = explode("|", $value);
                    $stockEach = [];
                    $quantity = 0;
                    $total = 0;
                    $prod = [];
                    $profit = 0;
                    $tax = 0;
                    foreach($ids as $id){
                        $stock = $em->getRepository('AppBundle:Stock')
                            ->find($id);
                        $product = $stock->getProduct();
                        $quantity += $stock->getQuantity();
                        $profit += $product->getProfit($stock->getUnitCost(), $stock->getRetailCost(), $stock->getQuantity());
                        $tax += $product->getVat($stock->getUnitCost(), $stock->getRetailCost(), $stock->getQuantity());
                        $total += $stock->getRetailCost()*$stock->getQuantity();
                        $prod['productQuantity']  = $quantity;
                        $prod['productId'] = $stock->getProduct()->getId();
                        $prod['productCode'] = $stock->getProduct()->getProductCode();
                        $prod['productTax'] = $stock->getProduct()->getProductTax();
                        $prod['productName'] = $stock->getProduct()->getProductName();
                        $prod['productSales'] = $stock->getRetailCost()*$quantity;
                        $prod['productCost'] = $stock->getUnitCost()*$quantity;
                        $bp = $prod['productCost'];
                        $sp = $prod['productSales'];
                        $prod['profit'] = $profit;
                        $prod['tax'] = $tax;
                        $prod['total'] = $total;
                    }
                    $prodArray[] = $prod;
                }

                $data['specificStock'] = $prodArray;

        	} 

        	if ($data['dates']['from']) {
        		$from = new \DateTime($data['dates']['from']);
        		$to = new \DateTime($data['dates']['to']);
        		$from ->setTime(0, 0, 0);
        		$to ->setTime(0, 0, 0);
        		$to -> modify('+1 day');
        		$stocks = $em->getRepository('AppBundle:Stock')
        			->findAllForThisRange($from, $to, "sal");

                $arrayValues = [];
                foreach($stocks as $stock){
                    $arrayValues[]  = [$stock->getProduct()->getId(), $stock->getId()];
                }
                $vals = [];
                foreach($arrayValues as $val){
                    $vals[] = array_count_values($val);
                }
                
                $prod = [];
                $relatedArray = [];
                $relatedStock = [];
                foreach($vals as $key => $value){
                    $id = array_keys($value)[0];
                    $sId = array_keys($value)[1];
                    $relatedStock[] = [$id=>$sId];
                }

                $prodArray = [];

                $finale = array();
                array_walk_recursive($relatedStock, function($item, $key) use (&$finale){
                    $finale[$key] = isset($finale[$key]) ?  $item ."|". $finale[$key] : $item;
                });

                foreach($finale as $key => $value){
                    $ids = explode("|", $value);
                    $stockEach = [];
                    $quantity = 0;
                    $total = 0;
                    $prod = [];
                    foreach($ids as $id){
                        $stock = $em->getRepository('AppBundle:Stock')
                            ->find($id);
                        $product = $stock->getProduct();
                        $quantity += $stock->getQuantity();
                        $total += $stock->getRetailCost()*$stock->getQuantity();
                        $prod['productQuantity']  = $quantity;
                        $prod['productId'] = $stock->getProduct()->getId();
                        $prod['productCode'] = $stock->getProduct()->getProductCode();
                        $prod['productName'] = $stock->getProduct()->getProductName();
                        $prod['productTax'] = $stock->getProduct()->getProductTax();
                        $prod['productSales'] = $stock->getRetailCost()*$quantity;
                        $prod['productCost'] = $stock->getUnitCost()*$quantity;
                        $bp = $prod['productCost'];
                        $sp = $prod['productSales'];
                        $prod['profit'] = $product->getProfit($bp, $sp);
                        $prod['tax'] = $product->getVat($bp, $sp);
                        $prod['total'] = $total;
                    }
                    $prodArray[] = $prod;
                }

        		$data['rangeStock'] = $prodArray;

        	}

            if($download == "True" ){

                $appPath = $this->container->getParameter('kernel.root_dir');

                $html = $this->renderView('PDF/saleGrouped.html.twig', $data);

                $filename = sprintf("sold-%s.pdf", date('Ymd~his'));

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

		return $this->render('stock/grouped.html.twig', $data );

	}


    /**
     * @Route("/sth", name="stg")
     */
    public function somethingAction(){

    }


}