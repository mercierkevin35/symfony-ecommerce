<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Address;
use App\Form\AddressType;
use App\Classe\Cart;


class AccountAddressController extends AbstractController
{
    /**
     * @Route("/account/addresses", name="account_addresses")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig', [
            'controller_name' => 'AccountAddressController',
        ]);
    }

    /**
     * @Route("/account/add-address", name="add_address")
     */
    public function add(Cart $cart, Request $request): Response
    {
        $address = new Address;
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($address);
            $entityManager->flush();

            if($cart->get()){
                return $this->redirectToRoute('order');
            }
            return $this->redirectToRoute('account_addresses');
        }
        return $this->render('account/address_form.html.twig', [
            'controller_name' => 'AccountAddressController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/edit-address/{id}", name="edit_address")
     */
    public function edit(Request $request, int $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Address::class);
        $address = $repository->findOneById($id);

        if(!$address || ($address->getUser() != $this->getUser())){
            return $this->redirectToRoute('account_addresses');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('account_addresses');
        }
        return $this->render('account/address_form.html.twig', [
            'controller_name' => 'AccountAddressController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/delete-address/{id}", name="delete_address")
     */
    public function delete(Request $request, int $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Address::class);
        $address = $repository->findOneById($id);

        if($address || ($address->getUser() == $this->getUser())){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($address);
            $entityManager->flush();
            
        }

        return $this->redirectToRoute('account_addresses');
    }
}
