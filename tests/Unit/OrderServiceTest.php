<?php

namespace App\Tests\Unit;

use App\Entity\ProductVariant;
use App\Service\OrderService;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    public function testCanBuyProductIfStockAvailable()
    {
        $product = new ProductVariant();
        $product->setStock(10);
        $quantity = 5;
        $service = new OrderService();
        $this->assertTrue($service->canBuyProduct($product, $quantity));
    }

    public function testCannotBuyProductIfStockNotAvailable()
    {
        $product = new ProductVariant();
        $product->setStock(10);
        $quantity = 15;
        $service = new OrderService();
        $this->assertFalse($service->canBuyProduct($product, $quantity));
    }
}
