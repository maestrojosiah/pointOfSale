<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PDFController extends Controller  
{
    /**
     * Export to PDF
     * 
     * @Route("sale/pdf/download", name="download_sales_pdf")
     */
    public function downloadPDFAction()
    {
		$data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
		$entity = $this->getDoctrine()
			->getRepository('AppBundle:Sale')
			->loadAllSalesFromThisUser($user);

		$dataArray = [];
		$dataInfo = [];
		foreach($entity as $entry){
			$dataInfo['date'] = $entry->getOnDate();
			$dataInfo['receipt'] = $entry->getId();
			$dataInfo['saleTotal'] = $entry->getTotalSale();
			$dataInfo['paymentMode'] = $entry->getPaymentMode();
			$dataArray[] = $dataInfo;
		}

		$data['entity'] = $dataArray;

        $appPath = $this->container->getParameter('kernel.root_dir');

        $html = $this->renderView('PDF/pdf.html.twig', $data);

        $filename = sprintf('sale-%s.pdf', date('Ymd~his'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    /**
     * Send PDF to email
     * 
     * @Route("sale/pdf/send", name="send_pdf")
     */
    public function sendPDFAction(\Swift_Mailer $mailer)
    {
		$data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
		$entity = $this->getDoctrine()
			->getRepository('AppBundle:Sale')
			->loadAllSalesFromThisUser($user);

		$dataArray = [];
		$dataInfo = [];
		foreach($entity as $entry){
			$dataInfo['date'] = $entry->getOnDate();
			$dataInfo['receipt'] = $entry->getId();
			$dataInfo['saleTotal'] = $entry->getTotalSale();
			$dataInfo['paymentMode'] = $entry->getPaymentMode();
			$dataArray[] = $dataInfo;
		}

		$data['entity'] = $dataArray;

        $appPath = $this->container->getParameter('kernel.root_dir');

        $filename = sprintf('sale-%s.pdf', date('Ymd~his'));

    	$this->get('knp_snappy.pdf')->generateFromHtml(
		    $this->renderView('PDF/pdf.html.twig', $data),
		    "$appPath/../web/pdfFiles/$filename"
		);

	    $message = (new \Swift_Message('Hi there!'))
	        ->setFrom('maestrojosiah@gmail.com')
	        ->setTo('jshbr7@gmail.com')
	        ->setBody(
	            "Kindly find the attached report",
	            'text/plain'
	    );

        $file = $appPath . "/../web/pdfFiles/$filename";
        $data['file'] = $file;

		$message->attach(
		  \Swift_Attachment::fromPath($file)->setFilename($filename)
		);

	    $this->get('mailer')->send($message);

        return $this->redirectToRoute('homepage');
    }
}