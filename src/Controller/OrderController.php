<?php

namespace App\Controller;
use App\Form\OrderType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;


/**
 * @Route("/order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="order")
     */
    public function index(Cart $cart): Response
    {

        if (!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('add_address');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
            'action' => $this->generateUrl('order_summary')
        ]);

        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'form' => $form->createView(),
            'cart' => $cart->get()
        ]);
    }


    /**
     * @Route("/summary", name="order_summary", methods={"POST"})
     */
    public function add(Cart $cart, Request $request): Response
    {

        if (!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('add_address');
        }


        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $date = new \DateTime("now", new \DateTimeZone("Europe/Paris"));
            $carrier = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname() . ' ' . $delivery->getLastname() . '<br>';
            $delivery_content .= $delivery->getPhone() . '<br>';
            $delivery_content .= $delivery->getCompany() ? $delivery->getCompany() . '<br>' : '';
            $delivery_content .= $delivery->getAddress() . '<br>';
            $delivery_content .= $delivery->getZipCode() . ' ' . $delivery->getCity() . '<br>';
            $delivery_content .= $delivery->getCountry();

            // Enregistrer ma commande Order()
            $reference = $_SERVER['REQUEST_TIME'] . '-' . uniqid();
            $order = new Order();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carrier->getName());
            $order->setCarrierPrice($carrier->getPrice());
            $order->setDelivery($delivery_content);
            $order->setState($order::UNPAID);

            $entityManager->persist($order);

            // Enregistrer mes produits OrderDetails()
            $total = 0;
            foreach($cart->get() as $id => $infos){
                $price = $infos['product']->getPrice();
                $priceTimesQty = $price * $infos['qty'];
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProductId($infos['product']->getId());
                $orderDetails->setProductName($infos['product']->getName());
                $orderDetails->setQuantity($infos['qty']);
                $orderDetails->setPrice($price);
                $orderDetails->setTotal($priceTimesQty);
                $entityManager->persist($orderDetails);
                $total += $priceTimesQty;
            }

            $total += $carrier->getPrice();
            $entityManager->flush();
            
            return $this->render('order/add.html.twig', [
                'controller_name' => 'OrderController',
                'cart' => $cart->get(),
                'carrier' => $carrier,
                'delivery' => $delivery_content,
                'total' => $total/100,
                'reference' => $order->getReference()
            ]);

        }

        return $this->redirectToRoute('cart');


    }


}
