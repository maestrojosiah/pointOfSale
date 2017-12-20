<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Sale;
use AppBundle\Entity\Stock;

class AdjController extends Controller
{
    /**
     * @Route("/adjAjax_request", name="ajax_request_adj")
     */
    public function tableAdjRowAction(Request $request)
    {
        if($request->request->get('some_var_name')){
            $quantityValue = $request->request->get('quantity') ? $request->request->get('quantity') : 0 ;
            $fullId = explode('_',$request->request->get('some_var_name'));
            $id = $fullId[1];

            $user = $this->get('security.token_storage')->getToken()->getUser();

            $product = $this->getDoctrine()
                ->getRepository('AppBundle:Product')
                ->find($id);
            if(!$product){
              $arrData = [];
            } else {

              $em = $this->getDoctrine()->getManager();

              $stockIn = $em->getRepository('AppBundle:Stock')
                  ->findAllStockForThisProduct($product, "sto");
              $stockSold = $em->getRepository('AppBundle:Stock')
                  ->findAllStockForThisProduct($product, "sal");
              $stockReturned = $em->getRepository('AppBundle:Stock')
                  ->findAllStockForThisProduct($product, "ret");
              $stockAdjusted = $em->getRepository('AppBundle:Stock')
                  ->findAllStockForThisProduct($product, "adj");

              $returns        =  $stockReturned[0]['total'];
              $sales          =  $stockSold[0]['total'];
              $stock          =  $stockIn[0]['total'];
              $adjustments    =  $stockAdjusted[0]['total'];

              $goodsAvailable = $stock + $adjustments - $returns;
              if($goodsAvailable > 0) {
                $goodsSold      = $sales;
                $balance        = $goodsAvailable - $goodsSold; 
                $remainingStock = $balance / $product->getProductRetailPrice();       
              } else {
                $remainingStock = 0;
              }

              $data['remainingStock'] = $remainingStock;


              $itemName   =     $product->getProductName();
              $itemCode   =     $product->getProductCode();
              $price      =     $product->getProductRetailPrice();
              $quantity   =     $quantityValue;
              $total      =     $price*$quantity;

              $row = " 
              <tr id='Rw_$id'> 
                <td><span id='Cl_$id' class='btn btn-secondary glyphicon glyphicon-remove' aria-hidden='true'></span></td>
                <td><span id='IN_$id' class='btn btn-secondary'><b>".$itemCode."</b> | ".$itemName."</span></td>
                <td><span id='Pr_$id' class='btn btn-secondary'>".$price."</span></td>
                <td><span id='RS_$id' class='btn btn-danger'>".$remainingStock."</span></td>
                <td><span id='RSE_$id' class='btn btn-success'>".$remainingStock."</span></td>
                <td><span id='QtAdj_$id' class='btn btn-info'>".$quantity."</span></td>
                <td><span id='Tl_$id' class='btn btn-secondary'>".$total."</span></td>
              </tr>";

              $arrData = ['output' => $row ];

            }

            return new JsonResponse($arrData);
        }

        return $this->render('sell/index.html.twig');
    }

    /**
     * @Route("/get_additions_adj", name="get_additions_adj")
     */
    public function getAdditionsAdjAction(Request $request)
    {
        $stuff = [];
        $rows = $request->request->get('rows');
        $raws = $request->request->get('raws');

        if($rows){
          $chunks = array_chunk($rows, 7);
        } else {
          $chunks = [0,0,0,0,0,0,0];
        }


        $colArray = [];

        foreach($chunks as $chunk){
          $colArray[] = $chunk;
          if($raws){
            $products = [];
            foreach($raws as $raw){
              $fullId = explode('_',$raw);
              $id = $fullId[1];

              $product = $this->getDoctrine()
                ->getRepository('AppBundle:Product')
                ->find($id);
              $products[] = $product->getId();
            }
          } 
        }

        $priceSum = array_sum(array_column($colArray, 2));
        $qtySum = array_sum(array_column($colArray, 3));
        $totalSum = array_sum(array_column($colArray, 6));

        $stuff['priceSum'] = $priceSum;
        $stuff['qtySum'] = $qtySum;
        $stuff['totalSum'] = $totalSum;
        $stuff['chunks'] = $chunks;
        //$stuff['colArray'] = $colArray;
        if($raws){
          $stuff['product'] = $id;
          $stuff['products'] = $products;
        }
        $arrData = ['output' => $stuff ];
        return new JsonResponse($arrData);
    }

    /**
     * @Route("/save/adjustment", name="save_adj")
     */
    public function saveAdjustmentAction(Request $request)
    {
        $data = [];
        $transaction = $request->request->get('transaction');
        $details = $request->request->get('details');
        if($request->request->get('suspended')) {
          $paymentMode = $request->request->get('suspended');
        } else if ($request->request->get('transaction') != "Sale"){
          $paymentMode = $request->request->get('transaction');
        } else {
          $paymentMode = $request->request->get('paymentMode');
        }

        if($transaction == "Purchase"){
          $detailsRecord = $purchaseDetails;
        } else if ($transaction == "Return"){
          $detailsRecord = $returnsDetails;
        } else if ($transaction == "Adjustment"){
          $detailsRecord = $details;
        } else {
          $detailsRecord = "customer";
        }
        
        $em = $this->getDoctrine()->getManager();
        $last_entity = $em->getRepository('AppBundle:Sale')
          ->loadLastSaleEntry();

  
        if($transaction != "Sale"){
          $paymentMode = $transaction;
        } else if($paymentMode != "check" && $paymentMode != "creditCard" && $paymentMode != "mpesa" && $paymentMode != "suspended"){
          $paymentMode = "cash";
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userFName = $user->getFName();
        $userLName = $user->getLName();
        $userNameAbbrev = $userFName[0].$userLName[0];
        $sale = new Sale();

        $sale->setUser($user);
        if(!$last_entity){
            $sale->setOrigId(1);
        } else {
            $lastInputId = $last_entity->getId();
            $thisId = $lastInputId + 1;
            $sale->setOrigId($thisId);
        }

        $today = date("d-m-Y h:i:s");
        $sale->setOnDate(new \DateTime($today));
        $sale->setCustomer("NA");
        $sale->setTax("NA");
        $sale->setDiscount("NA");
        $sale->setPaymentMode($paymentMode);
        $sale->setQty(0);
        $sale->setDetails($detailsRecord);
        $sale->setTotalSale("NA");

        $em->persist($sale);
        $em->flush();
        $EntityId = $sale->getId();
        $receiptNumber = date("ymd").$EntityId.$userNameAbbrev;

        //$array = explode(",", $finalArray);

        //$newArray = array_push($array, $paidAmt);

        $saleNumber = ['output' => $receiptNumber ];
        return new JsonResponse($saleNumber);
    }

    /**
     * @Route("/save/adj/stock", name="save_stock_adj")
     */
    public function saveStockAdjAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $stockArray = $request->request->get('stock');
        $thisSaleTransaction = $request->request->get('transaction');
        $idArray = [];

        foreach($stockArray as $stockItem){
          $stock = new Stock();
          $prod = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->find($stockItem[0]);
          $idArray[] = $stockItem[0];
          $today = date("d-m-Y h:i:s");
          $stock->setOnDate(new \DateTime($today));
          $stock->setProduct($prod);
          $stock->setQuantity($stockItem[5]);
          $last_entity = $em->getRepository('AppBundle:Stock')
            ->loadLastStockEntry();
          $lastSale = $em->getRepository('AppBundle:Sale')
            ->loadLastSaleEntry();

          $stock->setSale($lastSale);
          $stock->setUser($user);
          if(!$last_entity){
              $stock->setOrigId(1);
          } else {
              $lastInputId = $last_entity->getId();
              $thisId = $lastInputId + 1;
              $stock->setOrigId($thisId);
          }
          $totalCost = $prod->getProductRetailPrice()*$stockItem[5];
          $stock->setUnitCost($prod->getProductCost());
          $stock->setRetailCost($prod->getProductRetailPrice());
          $stock->setTotalCost($totalCost);
          if($lastSale->getPaymentMode() == "suspended"){
            $stock->setTransaction("sus");
          } elseif ($thisSaleTransaction == "Sale") {
            $stock->setTransaction("sal");
          } elseif ($thisSaleTransaction == "Return") {
            $stock->setTransaction("ret");
          } elseif ($thisSaleTransaction == "Purchase") {
            $stock->setTransaction("sto");
          } elseif ($thisSaleTransaction == "Adjustment") {
            $stock->setTransaction("adj");
          } else {
            $stock->setTransaction("err");
          }
          $em->persist($stock);
          $em->flush();
        }


        $arrData = ['output' => $idArray ];
        return new JsonResponse($arrData);
    }

    //this is supposed to be in the ProductController.php
    /**
     * @Route("/find/product", name="search_for_this_code")
     */
    public function searchForCodeAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $codeValue = $request->request->get('code');

        $product = $em->getRepository('AppBundle:Product')
          ->findOneByProductCode($codeValue);

        if(!$product){
          $product = null;
        }

        if($product == null){
          $address = $this->generateUrl('product_add');
        } else {
          $address = $this->generateUrl('product_edit', ['productId' => $product->getId()]);
        }

        $arrData = ['output' => $address ];
        return new JsonResponse($arrData);
    }

    /**
     * @Route("/ajax_request_search_adj", name="ajax_request_search_adj")
     */
    public function tableSearchRowAction(Request $request)
    {
        $data = [];
        $code = $request->request->get('code');
        $qtyRemaining = $request->request->get('qtyRemaining');


        $product = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->findOneBy(
              array('productCode' => $code, 'deleted' => 0),
              array('id' => 'ASC')
            );
        if(!$product){
          $arrData = [];
        } else {
          $em = $this->getDoctrine()->getManager();

          $stockIn = $em->getRepository('AppBundle:Stock')
              ->findAllStockForThisProduct($product, "sto");
          $stockSold = $em->getRepository('AppBundle:Stock')
              ->findAllStockForThisProduct($product, "sal");
          $stockReturned = $em->getRepository('AppBundle:Stock')
              ->findAllStockForThisProduct($product, "ret");
          $stockAdjusted = $em->getRepository('AppBundle:Stock')
              ->findAllStockForThisProduct($product, "adj");

          $returns        =  $stockReturned[0]['total'];
          $sales          =  $stockSold[0]['total'];
          $stock          =  $stockIn[0]['total'];
          $adjustments    =  $stockAdjusted[0]['total'];
          $id             =  $product->getId();

          $goodsAvailable = $stock + $adjustments - $returns;
          if($goodsAvailable > 0) {
            $goodsSold      = $sales;
            $balance        = $goodsAvailable - $goodsSold; 
            $remainingStock = $balance / $product->getProductRetailPrice();       
          } else {
            $remainingStock = 0;
          }

          $data['remainingStock'] = $remainingStock;

          if($remainingStock > 1){
            $data['message'] = 'OK';
          } else {
            $data['message'] = "warningLowStock";
          }

          $itemName   =     $product->getProductName();
          $itemCode   =     $product->getProductCode();
          $price      =     $product->getProductRetailPrice();
          $quantity   =     1;
          $total      =     $price*$quantity;

          $data['row'] = " 
          <tr id='Rw_$id'> 
            <td><span id='Cl_$id' class='btn btn-secondary glyphicon glyphicon-remove' aria-hidden='true'></span></td>
            <td><span id='IN_$id' class='btn btn-secondary'><b>".$itemCode."</b> | ".$itemName."</span></td>
            <td><span id='Pr_$id' class='btn btn-secondary'>".$price."</span></td>
            <td><span id='RS_$id' class='btn btn-danger'>".$remainingStock."</span></td>
            <td><span id='RSE_$id' class='btn btn-success'>".$remainingStock."</span></td>
            <td><span id='QtAdj_$id' class='btn btn-info'>".$quantity."</span></td>
            <td><span id='Tl_$id' class='btn btn-secondary'>".$total."</span></td>
          </tr>";

          $arrData = ['output' => $data ];

        }

        return new JsonResponse($arrData);
    

    }


}