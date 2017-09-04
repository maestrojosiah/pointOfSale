<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MailController extends Controller
{

	/**
	 *@Route("/mail/{name}", name="send_email")
	 */
	public function indexAction($name, \Swift_Mailer $mailer)
	{
	    $message = (new \Swift_Message('Hi there!'))
	        ->setFrom('maestrojosiah@live.com')
	        ->setTo('jshbr7@gmail.com')
	        ->setBody(
	            $this->renderView(
	                // app/Resources/views/Emails/registration.html.twig
	                'mail/test.html.twig',
	                array('name' => $name)
	            ),
	            'text/html'
	        )
	        /*
	         * If you also want to include a plaintext version of the message
	        ->addPart(
	            $this->renderView(
	                'Emails/registration.txt.twig',
	                array('name' => $name)
	            ),
	            'text/plain'
	        )
	        */
	    ;
        $data = [];
        $appPath = $this->container->getParameter('kernel.root_dir');
        $file = realpath($appPath . '/../web/excelFiles/ABC.XLS');
        $data['file'] = $file;

		$message->attach(
		  \Swift_Attachment::fromPath($file)->setFilename('products.xls')
		);
	    // $mailer->send($message);

	    // or, you can also fetch the mailer service this way
	    $this->get('mailer')->send($message);

	    return $this->render('mail/test.html.twig', ['name'=>$name] );
	}

}