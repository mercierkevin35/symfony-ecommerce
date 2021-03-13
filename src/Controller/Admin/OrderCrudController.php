<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Doctrine\ORM\EntityManagerInterface;

class OrderCrudController extends AbstractCrudController
{
    private $em;
    private $urlGen;

    public function __construct(EntityManagerInterface $entityManager, CrudUrlGenerator $urlGenerator){
        $this->em = $entityManager;
        $this->urlGen = $urlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours')
                                ->linkToCrudAction('updatePreparation')
                            ;
        return $actions
                    ->add(Crud::PAGE_DETAIL, $updatePreparation)
                    ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureCrud(Crud $crud): Crud {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function updatePreparation(AdminContext $context){
        $order = $context->getEntity()->getInstance();
        $order->setState($order::PREPARE);
        $this->em->flush();

        $this->addFlash('notice', "<span style='color:green'><strong>La commande ".$order->getReference()." a bien été mise à jour</strong></span>");
        $url = $this->urlGen->build()
            ->setController(__CLASS__)
            ->setAction(Action::INDEX)
            ->generateUrl();
        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('reference', 'Référence'),
            TextField::new('user.fullName', 'Client'),
            DateTimeField::new('createdAt', 'Crée le'),
            ArrayField::new('OrderDetails', 'Produits')->hideOnIndex(),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            MoneyField::new('total')->setCurrency('EUR'),
            ChoiceField::new('state', 'Etat')->setChoices([
                'Non payée' => Order::UNPAID,
                'Payée' => Order::PAID,
                'Préparation en cours' => Order::PREPARE,
                'Livraison en cours' => Order::DELIVER
            ])
        ];
    }

}
