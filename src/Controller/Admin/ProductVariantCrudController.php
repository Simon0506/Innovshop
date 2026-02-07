<?php

namespace App\Controller\Admin;

use App\Entity\ProductVariant;
use App\Repository\OptionValueRepository;
use App\Validator\SelectedOptionValuesUniqueGroup;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductVariantCrudController extends AbstractCrudController
{
    public function __construct(private OptionValueRepository $optionValueRepository)
    {
        $this->optionValueRepository = $optionValueRepository;
    }

    public static function getEntityFqcn(): string
    {
        return ProductVariant::class;
    }

    public function configureFields(string $pageName): iterable
    {

        return [
            MoneyField::new('priceHT', 'Prix HT')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->setFormTypeOption('disabled', true)
                ->setColumns(12),

            NumberField::new('tva', 'TVA (%)')
                ->setFormTypeOption('empty_data', '20')
                ->setFormTypeOption('attr', [
                    'min' => 0,
                    'max' => 100,
                    'step' => 0.01,
                ])
                ->setColumns(12),

            MoneyField::new('tvaAmount', 'Montant de la TVA')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->setFormTypeOption('mapped', false)
                ->setFormTypeOption('disabled', true)
                ->setColumns(12),

            MoneyField::new('price', 'Prix TTC')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->setColumns(12),

            IntegerField::new('stock', 'Stock')
                ->setColumns(12),

            TextField::new('sku', 'SKU')->hideOnIndex()
                ->setColumns(12),

            ChoiceField::new('selectedOptionValues', 'Options')
                ->setChoices($this->getOptionValueChoices())
                ->allowMultipleChoices()
                ->renderExpanded()
                ->setFormTypeOption('constraints', [
                    new SelectedOptionValuesUniqueGroup(),
                ])
                ->onlyOnForms()
                ->setColumns(12),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        return new ProductVariant();
    }

    private function getOptionValueChoices(): array
    {
        $choices = [];

        foreach ($this->optionValueRepository->findAll() as $optionValue) {
            $label = sprintf(
                '%s — %s',
                $optionValue->getOptionGroup()->getName(),
                $optionValue->getValue()
            );

            $choices[$label] = $optionValue;
        }

        return ksort($choices) ? $choices : [];
    }
}
