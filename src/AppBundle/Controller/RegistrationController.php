<?php 

namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use AppBundle\Entity\Permission;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) See if any users exist
            $users = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:User')
                ->findAll();

            // 5) if first user, make admin
            if($users){
                // do nothing
            } else {
                $user->setAdmin(true);
            }

            // 6) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $userId = $user->getId();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('permissions', ['user_id' => $userId ]);
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/permissions/{user_id}", name="permissions")
     */
    public function permissionsAction(Request $request, $user_id)
    {

        $data = [];
        $user = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:User')
            ->find($user_id);
        $permissions = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Permission')
            ->findBy(
                array('user'=>$user),
                array('id' => 'DESC')
             );
        $perm_array = [];
        foreach($permissions as $perm){
            $perm_array[$perm->getFeature()] = $perm;
        }
        $data['user'] = $user;
        $data['permissions'] = $perm_array;
        return $this->render('registration/permissions.html.twig', $data);

    }

    /**
     * @Route("change/permissions", name="change_permissions")
     */
    public function permissionsChangeAction(Request $request)
    {

        $data = [];
        $users = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:User')
            ->findAll();

        $data['users'] = $users;

        return $this->render('registration/user_list.html.twig', $data);

    }

    /**
     * @Route("/permission/save", name="save_permissions")
     */
    public function savePermissionsAction(Request $request)
    {
            
        $em = $this->getDoctrine()->getManager();

        $purchases = $request->request->get('purchases');
        $returns  = $request->request->get('returns');
        $tax = $request->request->get('tax');
        $sales = $request->request->get('sales');
        $stock_mov = $request->request->get('stock_mov');
        $stock_adj = $request->request->get('stock_adj');
        $stock_take = $request->request->get('stock_take');
        $pos_summary = $request->request->get('pos_summary');
        $sales_reports = $request->request->get('sales_reports');
        $user_id = $request->request->get('user_id');

        $user = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:User')
            ->find($user_id);

        $features_array = ['purchases'=>$purchases, 'returns'=>$returns, 'tax'=>$tax, 'sales'=>$sales, 'stock_mov'=>$stock_mov, 'stock_adj'=>$stock_adj, 'stock_take'=>$stock_take, 'pos_summary'=>$pos_summary, 'sales_reports'=>$sales_reports];

        foreach($features_array as $feature=>$perm){
            $existing = $em->getRepository('AppBundle:Permission')
                ->findBy(
                    array('user'=>$user, 'feature'=>$feature),
                    array('id' => 'DESC')
                );
            $permission = $existing ? $existing[0] : new Permission();
            $permission->setFeature($feature);
            $permission->setUser($user);
            $permission->setRights($perm);
            $em->persist($permission);
            $em->flush();
        }
        $data = [];
        $data['features_array'] = $features_array;

        return new JsonResponse($data);
    }



}