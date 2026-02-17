<?php

namespace App\Service;

use App\Entity\ProductVariant;

class OrderService
{
    public function canBuyProduct(ProductVariant $product, int $quantity): bool
    {
        return $product->getStock() >= $quantity;
    }
}
