<?php 

namespace AppBundle\Controller;

use AppBundle\Form\CategoryType;
use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{

	/**
	 * @Route("category/add", name="category_add")
	 */
	public function createAction(Request $request)
	{

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
        throw $this->createAccessDeniedException();
        }
        $data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $data['user'] = $user;

		// 1) build the form
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

			$last_entity = $em->getRepository('AppBundle:Category')
				->loadLastCategoryEntry();

            $category->setUser($user);

	            if(!$last_entity){
	            	$category->setOrigId(1);
	            } else {
	            	$lastInputId = $last_entity->getId();
	            	$thisId = $lastInputId + 1;
	            	$category->setOrigId($thisId);
	        	}

            $em->persist($category);
            $em->flush();

        	$this->addFlash(
	            'success',
	            'Category added successfully!'
        	);

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            if($form->get('saveAndAdd')->isClicked()){
                return $this->redirectToRoute('category_add');
            } else {
                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render(
            'category/add.html.twig',
            array('form' => $form->createView())
        );


	}

	/**
	 * @Route("/category/edit/{categoryId}", name="category_edit")
	 */
	public function editAction(Request $request, $categoryId)
	{
		$data = [];
		$category = $this->getDoctrine()
			->getRepository('AppBundle:Category')
			->find($categoryId);

		$form = $this->createForm(CategoryType::class, $category);

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$form_data = $form->getData();
			$data['form'] = $form_data;
			$em = $this->getDoctrine()->getManager();
			$em->persist($form_data);
			$em->flush();

        	$this->addFlash(
	            'success',
	            'Category edited successfully!'
        	);

			return $this->redirectToRoute('category_list');
		} else {
			$form_data['category_name'] = $category->getCategoryName();
			$data['form'] = $form_data;
		}

		return $this->render('category/edit.html.twig', ['form' => $form->createView(), 'data' => $data,] );

	}

	/**
	 * @Route("/category/list", name="category_list")
	 */
	public function listAction()
	{
		$data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
		$categories = $this->getDoctrine()
			->getRepository('AppBundle:Category')
			->loadAllCategoriesFromThisUser($user);

		$data['categories'] = $categories;

		return $this->render('category/list.html.twig', ['data' => $data ] );

	}

	/**
	 * @Route("/category/restore", name="restore_list")
	 */	
	public function restoreListAction()
	{
		$data = [];
        $user = $this->get('security.token_storage')->getToken()->getUser();
		$categories = $this->getDoctrine()
			->getRepository('AppBundle:Category')
			->loadAllDeletedCategoriesFromThisUser($user);

		$data['categories'] = $categories;

		return $this->render('category/restore.html.twig', ['data' => $data ] );

	}

	/**
	 * @Route("/category/delete/{categoryId}", name="category_delete")
	 */
	public function deleteAction($categoryId)
	{
		$em = $this->getDoctrine()->getManager();

		$category = $em->getRepository('AppBundle:Category')
			->find($categoryId);

		if(!$category) {
			throw $this->createNotFoundException(
				'No category for this id'
			);
		}

		$category->setDeleted(1);
 
        	$this->addFlash(
	            'success',
	            'Category deleted successfully!'
        	);


		$em->persist($category);
		$em->flush(); 

		return $this->redirectToRoute('category_list');
	}

	/**
	 * @Route("/category/restore/{categoryId}", name="category_restore")
	 */
	public function restoreAction($categoryId)
	{
		$em = $this->getDoctrine()->getManager();

		$category = $em->getRepository('AppBundle:Category')
			->find($categoryId);

		if(!$category) {
			throw $this->createNotFoundException(
				'No category for this id'
			);
		}

		$category->setDeleted(0);

        	$this->addFlash(
	            'success',
	            'Category restored successfully!'
        	);

		$em->persist($category);
		$em->flush(); 

		return $this->redirectToRoute('restore_list');
	}

}