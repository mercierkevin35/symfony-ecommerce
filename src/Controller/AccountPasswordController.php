<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ChangePasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    /**
     * @Route("/account/edit-password", name="account_password")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $old_password = $form->get('old_password')->getData();
            if ($encoder->isPasswordValid($user, $old_password)){
                $new_password = $form->get('new_password')->getData();
                $user->setPassword($encoder->encodePassword($user, $new_password));
                $entityManager = $this->getDoctrine()->getManager();
                //$entityManager->persist($user); optionnel dans le cas d'une mise à jour des données
                $entityManager->flush();
                $notification = 'Votre mot de passe a bien été mis à jour';
            }
        }
        return $this->render('account/password.html.twig', [
            'controller_name' => 'AccountPasswordController',
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
