<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Sale;
use AppBundle\Entity\Stock;

class AjaxController extends Controller
{
    /**
     * @Route("/ajax_request", name="ajax_request")
     */
    public function tableRowAction(Request $request)
    {
        if($request->request->get('some_var_name')){
            $quantityValue = $request->request->get('quantity') ? $request->request->get('quantity') : 1 ;
            $fullId = explode('_',$request->request->get('some_var_name'));
            $id = $fullId[1];

            $user = $this->get('security.token_storage')->getToken()->getUser();

            $product = $this->getDoctrine()
                ->getRepository('AppBundle:Product')
                ->find($id);
            if(!$product){
              $arrData = [];
            } else {
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
                <td><span id='Qt_$id' class='btn btn-secondary'>".$quantity."</span></td>
                <td><span id='Tl_$id' class='btn btn-secondary'>".$total."</span></td>
              </tr>";

              $arrData = ['output' => $row ];

            }

            return new JsonResponse($arrData);
        }

        return $this->render('sell/index.html.twig');
    }

    /**
     * @Route("/remove/row", name="remove_request")
     */
    public function removeRowAction(Request $request)
    {
        if($request->request->get('some_var_name')){
            $fullId = explode('_',$request->request->get('some_var_name'));
            $id = $fullId[1];

            $data = ['output' => $id];

            }

            return new JsonResponse($id);
    }

    /**
     * @Route("/ajax_request_search", name="ajax_request_search")
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
          $itemName   =     $product->getProductName();
          $itemCode   =     $product->getProductCode();
          $price      =     $product->getProductRetailPrice();
          $quantity   =     "1";
          $total      =     $price*$quantity;
          $id         =     $product->getId();

          if($qtyRemaining > 1){
            $data['message'] = 'OK';
          } else {
            $data['message'] = "warningLowStock";
          }

            $data['row'] = " 
            <tr id='Rw_$id'> 
              <td><span id='Cl_$id' class='btn btn-secondary glyphicon glyphicon-remove' aria-hidden='true'></span></td>
              <td><span id='IN_$id' class='btn btn-secondary'><b>".$itemCode."</b> | ".$itemName."</span></td>
              <td><span id='Pr_$id' class='btn btn-secondary'>".$price."</span></td>
              <td><span id='Qt_$id' class='btn btn-secondary'>".$quantity."</span></td>
              <td><span id='Tl_$id' class='btn btn-secondary'>".$total."</span></td>
            </tr>";
          $arrData = ['output' => $data ];

        }

        return new JsonResponse($arrData);
    

    }

    /**
     * @Route("/get_id", name="get_id")
     */
    public function getIdAction(Request $request)
    {
        if($request->request->get('thisValue')){
            $fullId = explode('_',$request->request->get('thisValue'));

            $value = $fullId[1];

            $user = $this->get('security.token_storage')->getToken()->getUser();

            $arrData = ['output' => $value ];
            return new JsonResponse($arrData);
        }

    }

    /**
     * @Route("/get_value", name="get_value")
     */
    public function getValueAction(Request $request)
    {
            $value = $request->request->get('thisValue');

            $user = $this->get('security.token_storage')->getToken()->getUser();

            $arrData = ['output' => $value ];
            return new JsonResponse($arrData);
    }

    /**
     * @Route("/get_matches", name="get_matches")
     */
    public function getMatchesAction(Request $request)
    {
            $data = [];
            $value = $request->request->get('thisValue');

            $user = $this->get('security.token_storage')->getToken()->getUser();
            $data['user'] = $user;
            $em = $this->getDoctrine()->getManager();
            $product = $this->getDoctrine()
              ->getRepository('AppBundle:Product')
              ->findOneBy(
                array('productCode' => $value, 'deleted' => 0),
                array('id' => 'ASC')
              );

            if(!$product){
                $products = $em->getRepository('AppBundle:Product')
                  ->searchMatchingProducts($value);

                $prodArray = [];

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
                      $prodArray[] = array($product->getId(), $product->getProductName(), $product->getProductCode(), $product->getProductRetailPrice(), $remainingStock );
                      $data['remainingStock'] = $remainingStock;

                  }
                  $data['prodArray'] = $prodArray;
                }
            } else {
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
              $data['remainingStock'] = $remainingStock;
              $data['product'] = $product->getProductName();
              $data['id'] = $product->getId();
              $data['code'] = $product->getProductCode();
              $data['price'] = $product->getProductRetailPrice();
            }

            $arrData = ['output' => $data ];
            return new JsonResponse($arrData);
    }

    /**
     * @Route("/ajax/get_products", name="get_products_ajax")
     */
    public function getProductsAction(Request $request)
    {
            $data = [];
            $category = $request->request->get('category');

            $user = $this->get('security.token_storage')->getToken()->getUser();
            $data['user'] = $user;
            $em = $this->getDoctrine()->getManager();
            $product = $this->getDoctrine()
              ->getRepository('AppBundle:Product')
              ->findOneBy(
                array('productCode' => $category, 'deleted' => 0),
                array('id' => 'ASC')
              );

            $products = $this->getDoctrine()
              ->getRepository('AppBundle:Product')
              ->loadAllProductsFromThisCategory($category);

            $prodArray = [];

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
                  $goodsSold      = $sales - $returns;
                  $balance        = $goodsAvailable - $goodsSold; 
                  $remainingStock = $balance / $product->getProductRetailPrice();       
                } else {
                  $remainingStock = 0;
                }
  
                $prodArray[] = array($product->getId(), $product->getProductName(), $product->getProductCode(), $product->getProductRetailPrice(), $remainingStock );
              }
              $data['prodArray'] = $prodArray;
            }

            $arrData = ['output' => $data ];
            return new JsonResponse($arrData);
    }

    /**
     * @Route("/ajax/get_purchases", name="get_purchases_ajax")
     */
    public function getPurchasesAction(Request $request)
    {
            $data = [];
            $what_to_do = $request->request->get('what_to_do') ? $request->request->get('what_to_do') : null ;

            $user = $this->get('security.token_storage')->getToken()->getUser();
            $data['user'] = $user;
            $em = $this->getDoctrine()->getManager();
            if($what_to_do == "get_purchases"){

              $purchases = $em->getRepository('AppBundle:Sale')
                  ->loadPurchases('Purchase');

              if(!$purchases){
                $data['purchases'] = null;
              } else {
                $data['purchases'] = $purchases;
              }
              $list = "<option selected >Select existing Purchase</option>";
              foreach($purchases as $purchase){
                $detailsArray = explode("|", $purchase->getPaymentMode());
                $refNo = isset($detailsArray[1]) ? $detailsArray[1] : 'Not Set' ;
                $number = isset($detailsArray[2]) ? $detailsArray[2] : 'Not Set' ;
                $list .= '<option value="'.$purchase->getId().'">'.'Number: '.$number.' Ref.No ['.$refNo .']</option>';
              }
              $to_send = $list;

            } else if ($what_to_do == "show_details"){

              $valueInSelect = $request->request->get('valueInSelect');
              $purchase = $em->getRepository('AppBundle:Sale')
                ->find($valueInSelect);
              $saleId = $purchase->getId();
              $to_send = $this->generateUrl('purchase_view', ['saleId'=>$saleId]);
            }

             

            $arrData = ['output' => $to_send ];
            return new JsonResponse($arrData);
    }

    /**
     * @Route("/ajax/get_returns", name="get_returns_ajax")
     */
    public function getReturnsAction(Request $request)
    {
            $data = [];
            $what_to_do = $request->request->get('what_to_do') ? $request->request->get('what_to_do') : null ;

            $user = $this->get('security.token_storage')->getToken()->getUser();
            $data['user'] = $user;
            $em = $this->getDoctrine()->getManager();
            if($what_to_do == "get_returns"){

              $returns = $em->getRepository('AppBundle:Sale')
                  ->loadPurchases('Return');

              if(!$returns){
                $data['returns'] = null;
              } else {
                $data['returns'] = $returns;
              }
              $list = "<option selected >Select existing Returns</option>";
              foreach($returns as $return){
                $detailsArray = explode("|", $return->getPaymentMode());
                $refNo = isset($detailsArray[1]) ? $detailsArray[1] : 'Not Set' ;
                $number = isset($detailsArray[2]) ? $detailsArray[2] : 'Not Set' ;
                $list .= '<option value="'.$return->getId().'">'.'Number: '.$number.' Ref.No ['.$refNo .']</option>';
              }
              $to_send = $list;

            } else if ($what_to_do == "show_details"){

              $valueInSelect = $request->request->get('valueInSelect');
              $return = $em->getRepository('AppBundle:Sale')
                ->find($valueInSelect);
              $saleId = $return->getId();
              $to_send = $this->generateUrl('return_view', ['saleId'=>$saleId]);
            }

             

            $arrData = ['output' => $to_send ];
            return new JsonResponse($arrData);
    }

    /**
     * @Route("/ajax/ref_no/{trans}", name="set_ref_no")
     */
    public function purchaseReferenceAction(Request $request, $trans)
    {
            $data = [];
            $value = $request->request->get('value') ? $request->request->get('value') : null ;
            $id = $request->request->get('id');

            $user = $this->get('security.token_storage')->getToken()->getUser();
            $data['user'] = $user;
            $em = $this->getDoctrine()->getManager();

            $sale = $em->getRepository('AppBundle:Sale')
              ->find($id); 

            if($trans == 'purchase'){
              $totalNumberOfPurchases = $em->getRepository('AppBundle:Sale')
                  ->countEntries('Purchase', $sale->getId());
              $increment = $totalNumberOfPurchases;            
              $sale->setPaymentMode("Purchase|".$value."|".$increment);
              $em->persist($sale);
              $em->flush();
              $url = $this->generateUrl('purchase_view', ['saleId'=>$id]);
            } else if ($trans == 'returns'){
              $totalNumberOfReturns = $em->getRepository('AppBundle:Sale')
                  ->countEntries('Return', $sale->getId());
              $increment = $totalNumberOfReturns;            
              $sale->setPaymentMode("Return|".$value."|".$increment);
              $em->persist($sale);
              $em->flush();
              $url = $this->generateUrl('return_view', ['saleId'=>$id]);
            }
            
            $arrData = ['output' => $url ];
            return new JsonResponse($arrData);
    }

    /**
     * @Route("/ajax/company_address/{trans}", name="set_company_address")
     */
    public function companyAddressAction(Request $request, $trans)
    {
            $data = [];
            $company = $request->request->get('company');
            $address = $request->request->get('address');
            $id = $request->request->get('id');

            $user = $this->get('security.token_storage')->getToken()->getUser();
            $data['user'] = $user;
            $em = $this->getDoctrine()->getManager();

            $sale = $em->getRepository('AppBundle:Sale')
              ->find($id); 

            $sale->setDetails($company.'|'.$address);
            $em->persist($sale);
            $em->flush();
            if($trans == 'purchase'){
              $url = $this->generateUrl('purchase_view', ['saleId'=>$id]);
            } else if ($trans == 'returns'){
              $url = $this->generateUrl('return_view', ['saleId'=>$id]);
            }

            
            $arrData = ['output' => $url ];
            return new JsonResponse($arrData);
    }

    /**
     * @Route("/ajax/get_categories", name="get_categories")
     */
    public function getCategoriesAction(Request $request)
    {
            $data = [];
            $value = $request->request->get('categories');

            $user = $this->get('security.token_storage')->getToken()->getUser();
            $data['user'] = $user;
            $em = $this->getDoctrine()->getManager();
            $categories = $this->getDoctrine()
              ->getRepository('AppBundle:Category')
              ->findBy(
                array('deleted' => 0),
                array('id' => 'ASC')
              );

                if(!$categories){
                  //do nothing
                } else {
                  foreach($categories as $category){
                    $catArray[] = array($category->getId(), $category->getCategoryName() );
                  }
                  $data['catArray'] = $catArray;
                }

            $arrData = ['output' => $data ];
            return new JsonResponse($arrData);
    }

    /**
     * @Route("/calculate/final", name="final_array")
     */
    public function getFinalCalculations(Request $request)
    {
            $data = [];
            $finalArray = $request->request->get('finalArray');
            $paidAmt = $request->request->get('paidAmt');
            $tax = $request->request->get('tax');
            $array = explode(",", $finalArray);

            //$newArray = array_push($array, $paidAmt);

            $arrData = ['output' => $array ];
            return new JsonResponse($arrData);
    }

    /**
     * @Route("/save/sale", name="save_sale")
     */
    public function saveSaleAction(Request $request)
    {
        $data = [];
        $transaction = $request->request->get('transaction');
        $qtyTotal = $request->request->get('qtyTotal');
        $total = $request->request->get('total');
        $mpesa = $request->request->get('mpesa');
        $creditCard = $request->request->get('creditCard');
        $purchaseDetails = $request->request->get('purchaseDetails');
        $returnsDetails = $request->request->get('returnsDetails');
        $referenceNumber = $request->request->get('referenceNumber');
        $referenceNumberR = $request->request->get('referenceNumberR');
        $check = $request->request->get('check');
        $tax = $request->request->get('tax');
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
        } else {
          $detailsRecord = "customer";
        }
        
        if($paymentMode == "check"){
          $append = "|".$check;
        } else if ($paymentMode == "creditCard"){
          $append = "|".$creditCard;
        } else if ($paymentMode == "mpesa"){
          $append = "|".$mpesa;
        } else {
          $append = "";
        }

        $em = $this->getDoctrine()->getManager();
        $last_entity = $em->getRepository('AppBundle:Sale')
          ->loadLastSaleEntry();


        if($transaction == "Purchase"){
          $totalNumberOfPurchases = $em->getRepository('AppBundle:Sale')
              ->countEntries('Purchase', $last_entity);
          $increment = $totalNumberOfPurchases + 1;
          $append = "|".$referenceNumber."|".$increment;
        } else if ($transaction == "Return"){
          $totalNumberOfReturns = $em->getRepository('AppBundle:Sale')
              ->countEntries('Return', $last_entity);
          $increment = $totalNumberOfReturns + 1;
          $append = "|".$referenceNumberR."|".$increment;
        }
  
        if($transaction != "Sale"){
          $paymentMode = $transaction;
        } else if($paymentMode != "check" && $paymentMode != "creditCard" && $paymentMode != "mpesa" && $paymentMode != "suspended"){
          $paymentMode = "cash";
        }
        if($request->request->get('suspended')) {
            $paymentMode = 'suspended|'.$request->request->get('suspended');
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
        $sale->setCustomer("Walk In Client");
        $sale->setTax($tax);
        $sale->setDiscount("0");
        $sale->setPaymentMode($paymentMode.$append);
        $sale->setQty($qtyTotal);
        $sale->setDetails($detailsRecord);
        $sale->setTotalSale($total);

        $em->persist($sale);
        $em->flush();
        $EntityId = $sale->getId();
        $receiptNumber = date("ymd").$EntityId.$userNameAbbrev;

        //$array = explode(",", $finalArray);

        //$newArray = array_push($array, $paidAmt);

        $saleNumber = ['output' => $paymentMode ];
        return new JsonResponse($saleNumber);
    }

    /**
     * @Route("/save/stock", name="save_stock")
     */
    public function saveStockAction(Request $request)
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
          $stock->setQuantity($stockItem[3]);
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
          $totalCost = $prod->getProductRetailPrice()*$stockItem[3];
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
          } else {
            $stock->setTransaction("err");
          }
          $em->persist($stock);
          $em->flush();
        }


        $arrData = ['output' => $idArray ];
        return new JsonResponse($arrData);
    }

    /**
     * @Route("/get_additions", name="get_additions")
     */
    public function getAdditionsAction(Request $request)
    {
        $stuff = [];
        $rows = $request->request->get('rows');
        $raws = $request->request->get('raws');

        if($rows){
          $chunks = array_chunk($rows, 5);
        } else {
          $chunks = [0,0,0,0,0];
        }


        $colArray = [];

        foreach($chunks as $chunk){
          $colArray[] = $chunk;
          if($raws){
            $profit = 0;
            $tax = 0;
            $products = [];
            foreach($raws as $raw){
              $fullId = explode('_',$raw);
              $id = $fullId[1];

              $product = $this->getDoctrine()
                ->getRepository('AppBundle:Product')
                ->find($id);
              if($product->getProductTax() == 1.16){
                $tax += $this->getVat($product->getProductCost(), $product->getProductRetailPrice(), $chunk[3]);
              } 
              if($product->getProductTax() == 1) {
                $tax += 0;
              }
              $profit += $this->getProfit($product->getProductCost(), $product->getProductRetailPrice(), $chunk[3]);
              $products[] = $product->getId();
            }
          } else {
            $tax = 0;
            $profit = 0;
          }
        }

        $priceSum = array_sum(array_column($colArray, 2));
        $qtySum = array_sum(array_column($colArray, 3));
        $totalSum = array_sum(array_column($colArray, 4));

        $stuff['priceSum'] = $priceSum;
        $stuff['qtySum'] = $qtySum;
        $stuff['totalSum'] = $totalSum;
        $stuff['chunks'] = $chunks;
        //$stuff['colArray'] = $colArray;
        if($raws){
          $stuff['tax'] = $tax;
          $stuff['profit'] = $profit;
          $stuff['product'] = $id;
          $stuff['products'] = $products;
        }
        $arrData = ['output' => $stuff ];
        return new JsonResponse($arrData);
    }

    public function getProfit($bp, $sp, $qty = 1, $rate = 16){
        $vat = round($sp * ($rate / 116), 2);
        $diff = $sp - $bp;
        $profit = ($diff - $vat) * $qty;
        return round($profit, 2);
    }
    
    public function getVat($bp, $sp, $qty = 1, $rate = 16) {
        $vat = $sp * ($rate / 116) * $qty;
        return round($vat, 2);
    }

}
