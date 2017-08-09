<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SellController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        $data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $products = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->loadAllProductsFromThisUser($user);
        $data['products'] = $products;
        $categories = [];
        foreach($products as $product){
            $categories[] = $product->getCategory();
        }
        $data['categories'] = array_unique($categories);

        $chunks = array_chunk($products, 4);
        $data['chunks'] = $chunks;

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'data' => $data,
            'chunks' => $chunks,            
        ]);
    }

    /**
     * @Route("/admin")
     */
    public function adminAction()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }

    /**
     * @Route("/hello", name="greetings")
     */
    public function helloAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
        throw $this->createAccessDeniedException();
        }
        $data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $data['user'] = $user;

        return $this->render('default/test.html.twig', [
            'data' => $data,
        ]);

    }
}