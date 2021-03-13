<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use App\Entity\Product;


class EasyAdminSubscriber implements EventSubscriberInterface {

    private $appKernel;
    private const DEST = "public" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "files";

    public function __construct(KernelInterface $appKernel){
        $this->appKernel = $appKernel;
    }

    public static function getSubscribedEvents() {
        return [
            BeforeEntityUpdatedEvent::class => ['beforeUpdate'],
            BeforeEntityPersistedEvent::class => ['beforePersist'],
            AfterEntityDeletedEvent::class => ['afterDelete']
        ];
    }

    public function updateIllustration(Product $entity){
        $filename = $_FILES['Product']['unique_name']['illustration']['file'];
        $entity->setIllustration($filename);
    }

    public function deleteIllustration(Product $entity){
        $path = $this->appKernel->getProjectDir() . DIRECTORY_SEPARATOR . self::DEST;
        $file = $entity->getIllustration();
        try{
            unlink($path . DIRECTORY_SEPARATOR . $file);
        }catch(\Exception $e){
            echo "Il y a eu un problème lors de la suppression de l'illustration :\n" . $e->getMessage();
        }
    }

    public function beforeUpdate(BeforeEntityUpdatedEvent $event){
        $entity = $event->getEntityInstance();
        if(get_class($entity) == Product::class && !empty($_FILES['Product']['name']['illustration']['file'])){
            $this->deleteIllustration($entity);
            $this->updateIllustration($entity);
        }
    }

    public function beforePersist(BeforeEntityPersistedEvent $event){
        $entity = $event->getEntityInstance();
        if(get_class($entity) == Product::class){
            $this->updateIllustration($entity);
        }
        
    }

    public function afterDelete(AfterEntityDeletedEvent $event){
        $entity = $event->getEntityInstance();
        if(get_class($entity) == Product::class){
            $this->deleteIllustration($entity);
        }
    }
}