<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{

	/**
	 * @Route("/product/list", name="product_list")
	 */
	public function listAction(Request $request) 
	{
		$data = [];
		return $this->render('product/list.html.twig', ['data' => $data ]);
		
	}

	/**
	 * @Route("/product/add", name="product_add")
	 */
	public function addAction(Request $request) 
	{
		$data = [];
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $data['user'] = $user;
        return $this->render('product/add.html.twig', ['data' => $data ]);

	}


}