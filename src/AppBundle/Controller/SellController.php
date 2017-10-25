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
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $products = $em->getRepository('AppBundle:Product')
            ->findBy(
                array('user' => $user),
                array('id' => 'ASC'),
                1
            );
        $data['products'] = $products;

        $categories = $em->getRepository('AppBundle:Category')
            ->findBy(
                array('user' => $user),
                array('id' => 'ASC'),
                1
            );

        $systSetting = $em->getRepository('AppBundle:SystSetting')
            ->settingsForThisUser($user);

        if(!$systSetting){
            return $this->redirectToRoute('systSetting_add');
        }
        if(!$categories){
            return $this->redirectToRoute('category_add');
        }
        if(!$products){
            return $this->redirectToRoute('product_add');
        }


        $data['categories'] = $categories;
        $data['systSetting'] = $systSetting;

        $chunks = array_chunk($products, 4);
        $data['chunks'] = $chunks;

        return $this->render('sell/index.html.twig', [
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

        return $this->render('sell/test.html.twig', [
            'data' => $data,
        ]);

    }
}