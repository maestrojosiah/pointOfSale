<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Product;

class FakeController extends Controller
{
    public function streamAction()
    {
        // create an empty object
        $phpExcelObject = $this->createXSLObject();
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=stream-file.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    public function storeAction()
    {
        // create an empty object
        $phpExcelObject = $this->createXSLObject();
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        $filename = tempnam(sys_get_temp_dir(), 'xls-') . '.xls';
        // create filename
        $writer->save($filename);

        return new Response($filename, 201);
    }

    public function readAndSaveAction()
    {
        $filename = $this->container->getParameter('xls_fixture_absolute_path');
        // create an object from a filename
        $phpExcelObject = $this->createXSLObject($filename);
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        $filename = tempnam(sys_get_temp_dir(), 'xls-') . '.xls';
        // create filename
        $writer->save($filename);

        return new Response($filename, 201);
    }

    /**
     * utility class
     * @return mixed
     */
    private function createXSLObject()
    {
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $htmlHelper = $this->get('phpexcel')->createHelperHTML();

        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Adventist Book Center")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Products list file");

        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAll();

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', $htmlHelper->toRichTextObject('<b>Code</b>'))
            ->setCellValue('B1', $htmlHelper->toRichTextObject('<b>Name</b>'))
            ->setCellValue('C1', $htmlHelper->toRichTextObject('<b>Cost</b>'))
            ->setCellValue('D1', $htmlHelper->toRichTextObject('<b>Taxable</b>'))
            ->setCellValue('E1', $htmlHelper->toRichTextObject('<b>Retail</b>'))
            ->setCellValue('G1', $htmlHelper->toRichTextObject('<b>Category</b>'));

        $counter = 2;
        foreach($products as $product){
            $phpExcelObject->getActiveSheet()
                ->setCellValue("A$counter", $product->getProductCode())
                ->setCellValue("B$counter", $product->getProductName())
                ->setCellValue("C$counter", $product->getProductCost())
                ->setCellValue("D$counter", $product->getProductTax())
                ->setCellValue("E$counter", $product->getProductRetailPrice())
                ->setCellValue("G$counter", $product->getCategory());
            $counter++;
        }

        foreach(range('B','G') as $columnID) {
            $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        return $phpExcelObject;
    }

    public function insertAction($file)
    {
        $data = [];
        $appPath = $this->container->getParameter('kernel.root_dir');
        $file = realpath($appPath . '/../web/excelFiles/'.$file);
        $data['file'] = $file;
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject($file);
        $sheet = $phpExcelObject->getActiveSheet()->toArray(null, true, true, true);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $data['user'] = $user;

        $em = $this->getDoctrine()->getManager();
        $data['sheet'] = $sheet;
        $dataProd = [];
        //READ EXCEL FILE CONTENT
        foreach($sheet as $i=>$row) {
        if($i !== 1) {
            $product = new Product();
            $category = $em->getRepository('AppBundle:Category')
                ->find($row['H']);

            if(!$category){
                $this->addFlash(
                    'notice',
                    'You need to add all the categories first!'
                );                
                return $this->redirectToRoute('category_add');
            }

            $last_entity = $em->getRepository('AppBundle:Product')
                ->loadLastProductEntry();

            $product->setUser($user);

                if(!$last_entity){
                    $product->setOrigId(1);
                } else {
                    $lastInputId = $last_entity->getId();
                    $thisId = $lastInputId + 1;
                    $product->setOrigId($thisId);
                }

            if($row['G'] == 'T'){
                $tax = 1.16;
            } else {
                $tax = 1;
            }

            $product->setProductCode($row['A']); 
            $product->setProductName($row['B']);
            $product->setProductRetailPrice($row['C']);
            $product->setProductCost($row['D']);
            $product->setProductTax($tax);
            $product->setCategory($category);
            //... and so on


            $em->persist($product);
            $em->flush();
            $dataProd[] = $product;
            }
            $data['product'] = $dataProd;
        }
        $data['obj'] = $phpExcelObject;
        //return $this->render('excel/read.html.twig', ['data' => $data ] );
        return $this->redirectToRoute('product_add');            
    }
}
