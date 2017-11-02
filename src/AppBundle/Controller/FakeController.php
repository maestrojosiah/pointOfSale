<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Product;
use AppBundle\Entity\Stock;

class FakeController extends Controller
{
    public function streamAction($entity)
    {
        // create an empty object
        $phpExcelObject = $this->createXSLObject($entity);
        //$data = $phpExcelObject = $this->createXSLObject("Product");

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename='.$entity.'.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
        //return $this->render('excel/test.html.twig', ['data'=>$data] ); 
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
    private function createXSLObject($entity)
    {
        $data = [];

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $htmlHelper = $this->get('phpexcel')->createHelperHTML();

        $phpExcelObject->getProperties()->setCreator("AdvBkCntr")
            ->setLastModifiedBy("Adventist Book Center")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Products list file");

        $em = $this->getDoctrine()->getManager();
        $records = $em->getRepository("AppBundle:$entity")
            ->findAll();
        $data['records'] = $records;
        if($entity == "Product"){

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A1', $htmlHelper->toRichTextObject('<b>Code</b>'))
                ->setCellValue('B1', $htmlHelper->toRichTextObject('<b>Name</b>'))
                ->setCellValue('C1', $htmlHelper->toRichTextObject('<b>Retail</b>'))
                ->setCellValue('D1', $htmlHelper->toRichTextObject('<b>Cost</b>'))
                ->setCellValue('E1', $htmlHelper->toRichTextObject('<b>Quantity</b>'))
                ->setCellValue('F1', $htmlHelper->toRichTextObject('<b>Tax</b>'))
                ->setCellValue('G1', $htmlHelper->toRichTextObject('<b>Taxable</b>'))
                ->setCellValue('H1', $htmlHelper->toRichTextObject('<b>Category</b>'));

            $counter = 2;
            foreach($records as $product){
                if($product->getProductTax() == "1.16"){
                    $taxable = "T";
                } else {
                    $taxable = "";
                }
                $phpExcelObject->getActiveSheet()
                    ->setCellValue("A$counter", $product->getProductCode())
                    ->setCellValue("B$counter", $product->getProductName())
                    ->setCellValue("C$counter", $product->getProductRetailPrice())
                    ->setCellValue("D$counter", $product->getProductCost())
                    ->setCellValue("E$counter", 0 )
                    ->setCellValue("F$counter", $product->getProductTax())
                    ->setCellValue("G$counter", $taxable)
                    ->setCellValue("H$counter", $product->getCategory()->getId());
                $counter++;
            }

            foreach(range('B','G') as $columnID) {
                $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }

            $phpExcelObject->getActiveSheet()->setTitle('Products');
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpExcelObject->setActiveSheetIndex(0);            
        }

        if($entity == "Sale"){

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A1', $htmlHelper->toRichTextObject('<b>Receipt#</b>'))
                ->setCellValue('B1', $htmlHelper->toRichTextObject('<b>Date</b>'))
                ->setCellValue('C1', $htmlHelper->toRichTextObject('<b>Sale Total</b>'))
                ->setCellValue('D1', $htmlHelper->toRichTextObject('<b>Payment Mode</b>'));

            $counter = 2;
            foreach($records as $entry){
                $datePrefix = $entry->getOnDate()->format('ymd');
                $phpExcelObject->getActiveSheet()
                    ->setCellValue("A$counter", $datePrefix.$entry->getId())
                    ->setCellValue("B$counter", $entry->getOnDate())
                    ->setCellValue("C$counter", $entry->getTotalSale())
                    ->setCellValue("D$counter", $entry->getPaymentMode());
                $counter++;
            }

            foreach(range('B','G') as $columnID) {
                $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }

            $phpExcelObject->getActiveSheet()->setTitle('Sales');
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpExcelObject->setActiveSheetIndex(0);            
        }

        if($entity == "Category"){

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A1', $htmlHelper->toRichTextObject('<b>Title</b>'))
                ->setCellValue('B1', $htmlHelper->toRichTextObject('<b>Code</b>'));
            $counter = 2;
            foreach($records as $entry){
                $phpExcelObject->getActiveSheet()
                    ->setCellValue("A$counter", $entry->getCategoryName())
                    ->setCellValue("B$counter", $entry->getId());
                $counter++;
            }

            foreach(range('A','C') as $columnID) {
                $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }

            $phpExcelObject->getActiveSheet()->setTitle('Categories');
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpExcelObject->setActiveSheetIndex(0);            
        }

        return $phpExcelObject;
        // return $data;           
    }

    public function insertAction($file)
    {
        set_time_limit(0);
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
            $qty = $row['E'];


            $em->persist($product);
            $em->flush();
            $dataProd[] = array("$qty" => $product);
            }
        }
        $data['products'] = $dataProd;

        $data['obj'] = $phpExcelObject;
        $new = [];
        foreach($dataProd as $dt){
            foreach($dt as $key => $prod){
                if($key > 0){
                    $stock = new Stock();
                    $today = date("d-m-Y h:i:s");
                    $stock->setOnDate(new \DateTime($today));
                    $stock->setProduct($prod);
                    $stock->setQuantity($key);
                    $stock->setTransaction("sto_ini");
                  $last_entity = $em->getRepository('AppBundle:Stock')
                    ->loadLastStockEntry();
                  $lastSale = $em->getRepository('AppBundle:Sale')
                    ->loadLastSaleEntry();

                  $stock->setSale($lastSale);
                  $stock->setUser($user);
                  if(!$last_entity){
                      $stock->setOrigId(1);
                  } else {
                      $lastInputId = $last_entity->getId();
                      $thisId = $lastInputId + 1;
                      $stock->setOrigId($thisId);
                  }
                  $totalCost = $prod->getProductRetailPrice()*$key;
                  $stock->setUnitCost($prod->getProductCost());
                  $stock->setRetailCost($prod->getProductRetailPrice());
                  $stock->setTotalCost($totalCost);
                  $em->persist($stock);
                    $em->flush();
                    $new[] = $stock;
                }
            }
        }
        $data['stock'] = $new;
        // return $this->render('excel/read.html.twig', ['data' => $data ] );
        return $this->redirectToRoute('product_add');            
    }
}
