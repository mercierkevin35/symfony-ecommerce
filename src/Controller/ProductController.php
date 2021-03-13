<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Classe\Search;
use App\Form\SearchType;

class ProductController extends AbstractController
{

    /**
     * @Route("/products", name="products")
     */
    public function index(Request $request): Response
    {
        $search = new Search;
        $form = $this->createForm(SearchType::class, $search);
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $products = $this->getDoctrine()->getRepository(Product::class)->findWithSearch($search);
        }

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product")
     */
    public function show(string $slug): Response
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBySlug($slug);

        if(!$product){
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig', [
            'controller_name' => 'ProductController',
            'product' => $product
        ]);
    }
}
