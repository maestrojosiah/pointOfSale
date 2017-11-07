<?php 

namespace AppBundle\Controller;

use AppBundle\Form\ProductType;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;

class ProductController extends Controller
{


	/**
	 * @Route("/product/add", name="product_add")
	 */
	public function addAction(Request $request) 
	{
		$data = [];
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $data['user'] = $user;

        $product = new Product();
        $product->setUser($user);
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

			$last_entity = $em->getRepository('AppBundle:Product')
				->loadLastProductEntry();

            $product->setUser($user);

	            if(!$last_entity){
	            	$product->setOrigId(1);
	            } else {
	            	$lastInputId = $last_entity->getId();
	            	$thisId = $lastInputId + 1;
	            	$product->setOrigId($thisId);
	        	}

            $em->persist($product);
            $em->flush();

        	$this->addFlash(
	            'success',
	            'Product added successfully!'
        	);

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user


            if($form->get('saveAndAdd')->isClicked()){
                return $this->redirectToRoute('product_add');
            } else {
                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render(
            'product/add.html.twig',
            array('form' => $form->createView())
        );

	}


	/**
	 * @Route("/product/edit/{productId}", name="product_edit")
	 */
	public function editAction(Request $request, $productId)
	{
		$data = [];
		$product = $this->getDoctrine()
			->getRepository('AppBundle:Product')
			->find($productId);

		$form = $this->createForm(ProductType::class, $product);

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$form_data = $form->getData();
			$data['form'] = $form_data;
			$em = $this->getDoctrine()->getManager();
			$em->persist($form_data);
			$em->flush();

        	$this->addFlash(
	            'success',
	            'Product edited successfully!'
        	);

            if($form->get('saveAndAdd')->isClicked()){
                return $this->redirectToRoute('product_add');
            } else {
                return $this->redirectToRoute('product_list');
            }

		} else {
			$form_data['product_code'] = $product->getProductCode();
			$form_data['product_name'] = $product->getProductName();
			$form_data['product_cost'] = $product->getProductCost();
			$form_data['product_retail_price'] = $product->getProductRetailPrice();
			$form_data['product_tax'] = $product->getProductTax();
			$data['form'] = $form_data;
		}

		return $this->render('product/edit.html.twig', ['form' => $form->createView(), 'data' => $data,] );

	}

	/**
	 * @Route("/product/list", name="product_list")
	 */
	public function listAction()
	{
		$data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
		$products = $this->getDoctrine()
			->getRepository('AppBundle:Product')
			->findBy(
				array('deleted' => 0),
				array('productName' => 'DESC')
			);

		$systSetting = $this->getDoctrine()
			->getRepository('AppBundle:systSetting')
			->settingsForThisUser($user);

		$data['products'] = $products;
		$data['systSetting'] = $systSetting;

		return $this->render('product/list.html.twig', ['data' => $data ] );

	}

	/**
	 * @Route("/product/restore", name="product_restore_list")
	 */	
	public function restoreListAction()
	{
		$data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
		$products = $this->getDoctrine()
			->getRepository('AppBundle:Product')
			->loadAllDeletedProductsFromThisUser($user);

		$data['products'] = $products;

		return $this->render('product/restore.html.twig', ['data' => $data ] );

	}

	/**
	 * @Route("/product/delete/{productId}", name="product_delete")
	 */
	public function deleteAction($productId)
	{
		$em = $this->getDoctrine()->getManager();

		$product = $em->getRepository('AppBundle:Product')
			->find($productId);

		if(!$product) {
			throw $this->createNotFoundException(
				'No product for this id'
			);
		}

		$product->setDeleted(1);

		$em->persist($product);
		$em->flush(); 

    	$this->addFlash(
            'success',
            'Product deleted successfully!'
    	);

		return $this->redirectToRoute('product_list');
	}

	/**
	 * @Route("/product/restore/{productId}", name="product_restore")
	 */
	public function restoreAction($productId)
	{
		$em = $this->getDoctrine()->getManager();

		$product = $em->getRepository('AppBundle:Product')
			->find($productId);

		if(!$product) {
			throw $this->createNotFoundException(
				'No product for this id'
			);
		}

		$product->setDeleted(0);

		$em->persist($product);
		$em->flush(); 

    	$this->addFlash(
            'success',
            'Product restored successfully!'
    	);

		return $this->redirectToRoute('product_restore_list');
	}

	/**
	 * @Route("/product/stock_take/{order_by}/{include_zero}/{download}", name="stock_take_report")
	 */
	public function stockTakeAction($order_by = 'productName', $include_zero = false, $download = "False")
	{
		$data = [];
 		$em = $this->getDoctrine()->getManager();
       	$user = $this->get('security.token_storage')->getToken()->getUser();
        if($order_by == 'productCode'){
			$products = $this->getDoctrine()
	            ->getRepository('AppBundle:Product')
		            ->findBy(
		              array('deleted' => 0),
		              array('productCode' => 'ASC')
		            );
        } else if ($order_by == 'productName'){
			$products = $this->getDoctrine()
	            ->getRepository('AppBundle:Product')
	            ->findBy(
	              array('deleted' => 0),
	              array('productName' => 'ASC')
	            );            	
        }

		$systSetting = $this->getDoctrine()
			->getRepository('AppBundle:systSetting')
			->settingsForThisUser($user);

		$stockReport = [];
	    if(!$products){
	      //do nothing
	    } else {
	      foreach($products as $product){
	        $stockIn = $em->getRepository('AppBundle:Stock')
	            ->findAllStockForThisProduct($product, "sto");
	        $stockSold = $em->getRepository('AppBundle:Stock')
	            ->findAllStockForThisProduct($product, "sal");
	        $stockReturned = $em->getRepository('AppBundle:Stock')
	            ->findAllStockForThisProduct($product, "ret");

	        $returns  =  $stockReturned[0]['total'];
	        $sales    =  $stockSold[0]['total'];
	        $stock    =  $stockIn[0]['total'];

	        $goodsAvailable = $stock - $returns;
	        if($goodsAvailable > 0) {
	          $goodsSold      = $sales;
	          $balance        = $goodsAvailable - $goodsSold; 
	          $remainingStock = $balance / $product->getProductRetailPrice();       
	        } else {
	          $remainingStock = 0;
	        }
		      if($include_zero == false){
		      	if($remainingStock > 1){
		      		$stockReport[$product->getId()."|".$product->getProductCode()."|".$product->getProductName()] = $remainingStock;
		      	}
		      } else {
		      	$stockReport[$product->getId()."|".$product->getProductCode()."|".$product->getProductName()] = $remainingStock;
		      }
	          

	      }
	      $data['stockReport'] = $stockReport;
	    }

        if($download == "True" ){
            $entity = $stockReport;

            foreach($entity as $product=>$entry){
                $dataInfo['Product Code'] = explode("|", $product)[1];
                $dataInfo['Product Name'] = explode("|", $product)[2];
                $dataInfo['Quantity On Hand'] = $entry;
                $dataInfo['Actual Count'] = "";
                $dataArray[] = $dataInfo;
            }

            $systSetting = $em->getRepository('AppBundle:systSetting')
                ->findOneByUser($user);
                
            $data['systSetting'] = $systSetting;
            $data['report'] = "Stock Take Report";


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


		$data['order_by'] = $order_by;
		$data['include_zero'] = $include_zero;
		$data['products'] = $products;
		$data['systSetting'] = $systSetting;

		return $this->render('product/stock_report.html.twig', ['data' => $data ] );

	}



}