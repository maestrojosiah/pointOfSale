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
     * @Route("/stock/received", name="items_received")
     */
    public function receivedAction(Request $request)
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
            

        }

        return $this->render('stock/received.html.twig', $data );

    }

    /**
     * @Route("/stock/returned", name="items_returned")
     */
    public function returnedAction(Request $request)
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
            

        }

        return $this->render('stock/returns.html.twig', $data );

    }

	/**
	 * @Route("/stock/compensated", name="items_compensated")
	 */
	public function compensatedAction(Request $request)
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
        			->findAllForThisDate($startDay, $endDay, "com");
        		$data['specificStock'] = $stocks;
        	} 

        	if ($data['dates']['from']) {
        		$from = new \DateTime($data['dates']['from']);
        		$to = new \DateTime($data['dates']['to']);
        		$from ->setTime(0, 0, 0);
        		$to ->setTime(0, 0, 0);
        		$to -> modify('+1 day');
        		$stocks = $em->getRepository('AppBundle:Stock')
        			->findAllForThisRange($from, $to, "com");
        		$data['rangeStock'] = $stocks;
        	}
        	

    	}

		return $this->render('stock/compensate.html.twig', $data );

	}

    /**
     * @Route("/stock/sold", name="items_sold")
     */
    public function soldAction(Request $request)
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
            

        }

        return $this->render('stock/sold.html.twig', $data );

    }

	/**
	 * @Route("/stock/tax", name="tax_report")
	 */
	public function taxAction(Request $request)
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
        			->findAllSoldForThisRange($from, $to, "sal");
        		$data['rangeStock'] = $stocks;
        	}
        	

    	}

		return $this->render('stock/tax.html.twig', $data );

	}

	/**
	 * @Route("/stock/grouped", name="items_sold_grouped")
	 */
	public function soldGroupedAction(Request $request)
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
                    $arrayValues[]  = [$stock->getProduct()->getId(), $stock->getQuantity(), $stock->getId()];
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
                    $qty = array_keys($value)[1];
                    $sId = array_keys($value)[2];
                    $relatedArray[] = [$id=>$qty];
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
                    $arrayValues[]  = [$stock->getProduct()->getId(), $stock->getQuantity(), $stock->getId()];
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
                    $qty = array_keys($value)[1];
                    $sId = array_keys($value)[2];
                    $relatedArray[] = [$id=>$qty];
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
        	

    	}

		return $this->render('stock/grouped.html.twig', $data );

	}





}