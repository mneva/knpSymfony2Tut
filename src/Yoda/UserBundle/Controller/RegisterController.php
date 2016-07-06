<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 6/8/16
 * Time: 11:02 PM
 */

namespace Yoda\UserBundle\Controller;

use Yoda\EventBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Yoda\UserBundle\Entity\User;
use Yoda\UserBundle\Form\RegisterFormType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegisterController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     *
     * @Template
     */
    public function registerAction(Request $request)
    {
        $defaultUser = new User();
        $defaultUser->setUsername('Leia');

        $form = $this->createForm(RegisterFormType::class, $defaultUser);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $user = $form->getData();

            $user->setPassword(
                $this->encodePassword($user, $user->getPlainPassword())
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Welcome to the Death Star, have a magical day!');

            $this->authenticateUser($user);

            $url = $this->generateUrl('event');

            return $this->redirect($url);
        }

        return array('form' => $form->createView());
    }

   /* private function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')
            ->getEncoder($user);

        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }*/

    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area';
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->getSecurityToken()->setToken($token);
    }
}