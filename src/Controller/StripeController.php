<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;

class StripeController extends AbstractController
{
    /**
     * @Route("/order/create-checkout-session/{reference}", methods={"POST"}, name="stripe_create_session")
     */
    public function index(Cart $cart, $reference): JsonResponse
    {
        $order = $this->getDoctrine()->getRepository(Order::class)->findOneByReference($reference);
        if(!$order){
            return new JsonResponse(['error' => 'order']);
        }

        $YOUR_DOMAIN = $_SERVER['HTTPS'] == 'On' ? 'https://' : 'http://';
        $YOUR_DOMAIN .= $_SERVER['HTTP_HOST'];
        Stripe::setApiKey('sk_test_51I1b1EEe3Qw4s31L7djEHuYsr4QDSQdlbfnI9dWyPuy4fwxXA7JXlOq5mdti9U4vTIFafed5XnEXsvOKhaIK89mu00FwfqKcx4');

        $product_for_stripe = [];
        foreach($order->getOrderDetails()->getValues() as $orderDetail){
            $product = $this->getDoctrine()->getRepository(Product::class)->findOneById($orderDetail->getProductId());
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $orderDetail->getPrice(),
                    'product_data' => [
                        'name' => $orderDetail->getProductName(),
                        'images' => [$YOUR_DOMAIN . '/uploads/files/' . $product->getIllustration()],
                    ],
                ],
                'quantity' => $orderDetail->getQuantity(),
            ];
        }

        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [],
                ],
            ],
            'quantity' => 1,
        ];

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $product_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/order/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/order/cancel/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(['id' => $checkout_session->id]);
    }
}
