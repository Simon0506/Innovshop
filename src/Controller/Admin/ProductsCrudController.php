<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Entity\ProductVariantOption;
use App\Repository\CategoriesRepository;
use BcMath\Number;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class ProductsCrudController extends AbstractCrudController
{
    public function __construct(private CategoriesRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
            ->setDefaultSort(['title' => 'ASC'])
            ->setPageTitle('index', 'Gestion des produits')
            ->setPageTitle('new', 'Ajouter un produit')
            ->setPageTitle('edit', 'Modifier le produit');
    }

    public function configureFields(string $pageName): iterable
    {

        return [
            TextField::new('title', 'Titre'),
            TextEditorField::new('description', 'Description'),
            AssociationField::new('category', 'Catégories')
                ->setFormTypeOptions([
                    'by_reference' => false,
                    'multiple' => true,
                    'choices' => $this->getCategoryChoices(),
                ])
                ->onlyOnForms(),
            TextField::new('categoryNames', 'Catégories')->onlyOnIndex(),
            CollectionField::new('images', 'Images')
                ->useEntryCrudForm(ProductImageCrudController::class)
                ->allowAdd()
                ->allowDelete()
                ->setEntryIsComplex()
                ->onlyOnForms()
                ->setFormTypeOption('row_attr', [
                    'data-controller' => 'image-preview',
                ]),
            MoneyField::new('price', 'Prix TTC (€)')
                ->setCurrency('EUR')
                ->setStoredAsCents(false),
            NumberField::new('stock', 'Stock')->hideOnForm(),
            BooleanField::new('une', 'À la une')->setFormTypeOption('required', false),
            DateTimeField::new('dateAddUne', 'Date mise à la une')->onlyOnIndex(),
            CollectionField::new('productVariants', 'Variantes')
                ->useEntryCrudForm(ProductVariantCrudController::class)
                ->allowAdd()
                ->allowDelete()
                ->onlyOnForms(),
        ];
    }

    public function persistEntity(EntityManagerInterface $em, $product): void
    {
        if (!$product instanceof Products) return;

        $this->updateProductVariantOptions($product, $em);

        $this->handleUneLimit($em, $product);

        parent::persistEntity($em, $product);
    }

    public function updateEntity(EntityManagerInterface $em, $product): void
    {
        if (!$product instanceof Products) return;

        $this->updateProductVariantOptions($product, $em);

        $this->handleUneLimit($em, $product);

        parent::updateEntity($em, $product);
    }

    private function handleUneLimit(EntityManagerInterface $em, Products $product)
    {
        if ($product->isUne()) {
            $product->setDateAddUne(new \DateTimeImmutable());
            $em->persist($product);
            $em->flush();

            $repository = $em->getRepository(Products::class);
            $productsUne = $repository->createQueryBuilder('p')
                ->where('p.une = :true')
                ->setParameter('true', true)
                ->orderBy('p.dateAddUne', 'ASC')
                ->getQuery()
                ->getResult();

            while (count($productsUne) > 3) {
                $oldest = array_shift($productsUne);
                $oldest->setUne(false);
                $oldest->setDateAddUne(null);
                $em->persist($oldest);
            }
            $em->flush();
        } else {
            $product->setDateAddUne(null);
            $em->persist($product);
            $em->flush();
        }
    }

    private function updateProductVariantOptions($product, EntityManagerInterface $em): void
    {
        foreach ($product->getProductVariants() as $variant) {
            $variant->getProductVariantOptions()->clear();
            foreach ($variant->getSelectedOptionValues() as $optionValue) {
                $vo = new ProductVariantOption();
                $vo->setProductVariant($variant);
                $vo->setOptionValue($optionValue);

                $variant->addProductVariantOption($vo);
                $em->persist($vo);
            }
            $em->persist($variant);
        }
        $em->persist($product);
        $em->flush();
    }

    private function getCategoryChoices(): array
    {
        $choices = [];
        foreach ($this->categoryRepository->findAll() as $category) {
            $choices[$category->getName()] = $category;
        }
        return $choices;
    }
}
