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
              $price      =     $product->getProductRetailPrice();
              $quantity   =     "1";
              $total      =     $price*$quantity;

              $row = " 
              <tr id='Rw_$id'> 
                <td><span id='Cl_$id' class='btn btn-secondary glyphicon glyphicon-remove' aria-hidden='true'></span></td>
                <td><span id='IN_$id' class='btn btn-secondary'>".$itemName."</span></td>
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
            $code = $request->request->get('code');


            $product = $this->getDoctrine()
                ->getRepository('AppBundle:Product')
                ->findOneByProductCode($code);
            if(!$product){
              $arrData = [];
            } else {
              $itemName   =     $product->getProductName();
              $price      =     $product->getProductRetailPrice();
              $quantity   =     "1";
              $total      =     $price*$quantity;
              $id         =     $product->getId();

              $row = " 
              <tr id='Rw_$id'> 
                <td><span id='Cl_$id' class='btn btn-secondary glyphicon glyphicon-remove' aria-hidden='true'></span></td>
                <td><span id='IN_$id' class='btn btn-secondary'>".$itemName."</span></td>
                <td><span id='Pr_$id' class='btn btn-secondary'>".$price."</span></td>
                <td><span id='Qt_$id' class='btn btn-secondary'>".$quantity."</span></td>
                <td><span id='Tl_$id' class='btn btn-secondary'>".$total."</span></td>
              </tr>";

              $arrData = ['output' => $row ];

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
            $product = $this->getDoctrine()
              ->getRepository('AppBundle:Product')
              ->findOneByProductCode($value);

            if(!$product){
              $data['product'] = "No Matches";
            } else {
              $data['product'] = $product->getProductName();
              $data['id'] = $product->getId();
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
        $qtyTotal = $request->request->get('qtyTotal');
        $total = $request->request->get('total');
        $mpesa = $request->request->get('mpesa');
        $creditCard = $request->request->get('creditCard');
        $check = $request->request->get('check');
        $tax = $request->request->get('tax');
        if($request->request->get('suspended')) {
          $paymentMode = $request->request->get('suspended');
        } else {
          $paymentMode = $request->request->get('paymentMode');
        }
        

        if($paymentMode != "check" && $paymentMode != "creditCard" && $paymentMode != "mpesa" && $paymentMode != "suspended"){
          $paymentMode = "cash";
        }
  
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $sale = new Sale();
        $em = $this->getDoctrine()->getManager();

        $last_entity = $em->getRepository('AppBundle:Sale')
          ->loadLastSaleEntry();

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
        $sale->setPaymentMode($paymentMode);
        $sale->setQty($qtyTotal);
        $sale->setTotalSale($total);

        $em->persist($sale);
        $em->flush();

        //$array = explode(",", $finalArray);

        //$newArray = array_push($array, $paidAmt);

        $arrData = ['output' => $paymentMode ];
        return new JsonResponse($arrData);
    }

    /**
     * @Route("/save/stock", name="save_stock")
     */
    public function saveStockAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $stockArray = $request->request->get('stock');
        $idArray = [];

        foreach($stockArray as $stockItem){
          $stock = new Stock();
          $prod = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->find($stockItem[0]);
          $idArray[] = $stockItem[0];
          $today = date("d-m-Y h:i:s");
          $stock->setOnDate(new \DateTime($today));
          $stock->setItemId($stockItem[0]);
          $stock->setQuantity($stockItem[3]);
          $stock->setTransaction("out");
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
          $stock->setUnitCost($prod->getProductCost());
          $stock->setRetailCost($prod->getProductRetailPrice());
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

              $tax += $this->getVat($product->getProductCost(), $product->getProductRetailPrice(), $chunk[3]);
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
