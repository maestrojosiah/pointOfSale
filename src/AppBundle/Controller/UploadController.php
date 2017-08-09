<?php 

namespace AppBundle\Controller;

use AppBundle\Form\UploadType;
use AppBundle\Entity\Upload;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UploadController extends Controller
{
	/**
	 * @Route("/product/upload", name="upload_product")
	 */
	public function uploadProductAction(Request $request){
		$data = [];
		$upload = new Upload();
		$form = $this->createForm(UploadType::class, $upload);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()){
			$excelFile = $upload->getFile();
			$data['excelFile'] = $excelFile;
			$originalName = $excelFile->getClientOriginalName();
	        $appPath = $this->container->getParameter('kernel.root_dir');
	        $filepath = realpath($appPath . '/../web/excelFiles/');
			$excelFile->move($filepath, $originalName);
			$data['orig'] = $originalName;
            return $this->redirectToRoute('fake_route_insert', ['file'=>$originalName]);
		}
        return $this->render('upload/product.html.twig', [
        	'form' => $form->createView(),
            'data' => $data,
        ]);
	}


}