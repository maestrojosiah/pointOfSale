<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DocController extends Controller
{
    /**
     * @Route("/doc", name="documentation")
     */
    public function tableRowAction(Request $request)
    {
    	$data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
    	$data['user'] = $user;
		return $this->render('default/doc.html.twig', $data );
    }


}
