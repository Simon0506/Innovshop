<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProductsCrudController extends AbstractCrudController implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            TextEditorField::new('description', 'Description'),
            ImageField::new('image', 'Image')
                ->setUploadDir('public/uploads')
                ->setBasePath('uploads'),
            NumberField::new('price', 'Prix'),
            IntegerField::new('stock', 'Stock'),
            AssociationField::new('category', 'Catégorie(s)')
                ->setFormTypeOptions(['by_reference' => false])
                ->setRequired(true)
                ->formatValue(fn($value) => $value instanceof \Doctrine\Common\Collections\Collection ? implode(', ', $value->map(fn($c) => $c->getName())->toArray()) : $value),
            AssociationField::new('option', 'Option(s)')
                ->setFormTypeOptions(['by_reference' => false])
                ->setRequired(false)
                ->formatValue(fn($value) => $value instanceof \Doctrine\Common\Collections\Collection ? implode(', ', $value->map(fn($o) => $o->getName())->toArray()) : $value),
            BooleanField::new('une', 'A la une'),
        ];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'checkFeaturedLimit',
            BeforeEntityUpdatedEvent::class   => 'checkFeaturedLimit',
        ];
    }

    public function checkFeaturedLimit(object $event): void
    {
        if (!method_exists($event, 'getEntityInstance')) {
            return;
        }

        $product = $event->getEntityInstance();
        if (!$product instanceof Products) {
            return;
        }

        if ($product->isUne()) {
            $this->handleFeaturedProducts($product);
        } else {
            $product->setDateAddUne(null);
        }
    }

    private function handleFeaturedProducts(Products $product): void
    {
        if (!$product->isUne()) return;

        if ($product->getDateAddUne() === null) {
            $product->setDateAddUne(new \DateTimeImmutable());
        }

        $featured = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Products::class, 'p')
            ->where('p.une = true')
            ->andWhere('p.id != :current')
            ->setParameter('current', $product->getId())
            ->orderBy('p.dateAddUne', 'ASC')
            ->getQuery()
            ->getResult();

        if (count($featured) >= 3) {
            $oldest = $featured[0];
            $oldest->setUne(false);
            $oldest->setDateAddUne(null);
            $this->em->persist($oldest);
        }
    }

    
}
