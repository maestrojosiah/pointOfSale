<?php 

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
	public function loginAction(Request $request, AuthenticationUtils $authUtils)
	{
	    // get the login error if there is one
	    $error = $authUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authUtils->getLastUsername();
	    $users = $this->getDoctrine()->getManager()
	    	->getRepository('AppBundle:User')
	    	->findBy(
	    		array('active' => true),
	    		array('id' => 'DESC')
	    	);

	    $data = [];
	    $data['users'] = $users;

	    return $this->render('security/login.html.twig', array(
	        'last_username' => $lastUsername,
	        'error'         => $error,
	        'data'			=> $data,
	    ));
	}

}