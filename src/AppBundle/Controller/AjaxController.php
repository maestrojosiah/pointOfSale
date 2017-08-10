<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
              <tr> 
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
     * @Route("/get_additions", name="get_additions")
     */
    public function getAdditionsAction(Request $request)
    {
        $data = [];
        $rows = $request->request->get('rows');

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $data['user'] = $user;

        $chunks = array_chunk($rows, 4);

        $colArray = [];

        foreach($chunks as $chunk){
          $colArray[] = $chunk;
        }

        $priceSum = array_sum(array_column($colArray, 1));
        $qtySum = array_sum(array_column($colArray, 2));
        $totalSum = array_sum(array_column($colArray, 3));

        $data['priceSum'] = $priceSum;
        $data['qtySum'] = $qtySum;
        $data['totalSum'] = $totalSum;
        $data['chunks'] = $chunks;

        $arrData = ['output' => $data ];
        return new JsonResponse($arrData);
    }
}
