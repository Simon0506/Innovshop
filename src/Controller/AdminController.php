<?php

namespace App\Controller;

use App\Entity\ProductVariant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin/product-variant/{id}/price', name: 'admin_product_variant_price', requirements: ['id' => '\d+'])]
    public function price(ProductVariant $variant): JsonResponse
    {
        return $this->json([
            'price' => $variant->getPrice(),
        ]);
    }
}
