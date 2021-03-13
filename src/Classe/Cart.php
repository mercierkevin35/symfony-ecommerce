<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

class Cart {
    private $session;
    private $em;

    public function __construct(SessionInterface $session, EntityManagerInterface $em){
        $this->em = $em;
        $this->session = $session;
    }


    public function increase(int $id, int $qty = 1): void{
        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id]['qty'])){
            $cart[$id]['qty'] += $qty;
        }else{
            $cart[$id]['qty'] = $qty;
        }

        $this->session->set('cart', $cart);
    }

    public function decrease(int $id, int $qty = 1): void{
        $cart = $this->session->get('cart', []);

        $cart[$id]['qty'] = $cart[$id]['qty'] > 0 ? $cart[$id]['qty'] - $qty: $cart[$id]['qty'];

        if($cart[$id]['qty'] <= 0){
            unset($cart[$id]);
        }

        $this->session->set('cart', $cart);
    }

    public function clear(int $id = null): void{
        if($id){
            $cart = $this->session->get('cart', []);
            if(!empty($cart[$id])){
                unset($cart[$id]);
            }
        } else {
            $cart = [];
        }
        $this->session->set('cart', $cart);
    }

    /**Returns all products in the cart if $id == null
     * else returns the product with id = $id or en empty array if it doesn't exist
     * Returns an array or array with the following keys :
     * - product : an instance of Product class
     * - qty : the the quantity of product product
     * 
     * @param $id the id of the product, default null
     * @return array
     */
    public function get(int $id = null){
        $this->cleanCartSession();
        $repository = $this->em->getRepository(Product::class);
        $cart = [];

        if($id && !empty($this->session->get('cart', [])[$id])){
            $qty = $this->session->get('cart', [])[$id]['qty'];
            $cart[$id] = [
                'product' => $repository->findOneById($id),
                'qty' => $qty
            ];

        } elseif (!$id) {
            foreach($this->session->get('cart', []) as $id => $infos){
                $cart[$id] = [
                    'product' => $repository->findOneById($id),
                    'qty' => $infos['qty']
                ];
            }
        }

        return $cart;
    }

    /**
     * Check if a product exists
     * @var int $id
     * @return bool
     */
    private function productExists(int $id){
        $repository = $this->em->getRepository(Product::class);

        return $repository->findOneById($id) ? true : false;
    }

    /**
     * Delete unexisting products
     * @return array
     */
    private function cleanCartSession(){
        foreach($this->session->get('cart', []) as $id => $infos){
            if(!$this->productExists($id)){
                $this->clear($id);
            }
        }
    }
}