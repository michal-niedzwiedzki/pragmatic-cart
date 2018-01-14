<?php

namespace Test\Epsi\PragmaticCart\Checkout;

use \PHPUnit\Framework\TestCase;
use \Epsi\PragmaticCart\Checkout\Cart;
use \Epsi\PragmaticCart\Store\Product;
use \Epsi\PragmaticCart\Checkout\LineItem;

/**
 * Test of cart
 *
 * @coversDefaultClass \Epsi\PragmaticCart\Checkout\Cart
 * @author Michał Rudnicki <michal@epsi.pl>
 */
class CartTest extends TestCase {

    /**
     * @test
     * @covers ::__construct
     * @covers ::getAvailablePromos
     */
    public function constructor_initializes_available_promos() {
        $promo = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $promos = [$promo, $promo];
        $cart = new Cart($promos);
        $this->assertSame($promos, $cart->getAvailablePromos());
    }

    /**
     * @test
     * @covers ::add
     * @covers ::remove
     * @covers ::getQuantityOf
     * @covers ::getLineItems
     */
    public function add_accumulates_remove_subtracts_getQuantity_counts_and_getLineItems_returns_them() {
        $cart = new Cart();
        $product1 = new Product("A", 1, 2, 3);
        $product2 = new Product("B", 2, 3, 4);

        // no items in cart initially
        $this->assertEmpty($cart->getLineItems());
        $this->assertEquals(0, $cart->getQuantityOf($product1));
        $this->assertEquals(0, $cart->getQuantityOf($product2));

        // add first product
        $cart->add($product1, 5);
        $this->assertCount(1, $cart->getLineItems());
        $this->assertEquals(5, $cart->getQuantityOf($product1));
        $this->assertEquals(0, $cart->getQuantityOf($product2));

        // add second product
        $cart->add($product2, 1);
        $this->assertCount(2, $cart->getLineItems());
        $this->assertEquals(5, $cart->getQuantityOf($product1));
        $this->assertEquals(1, $cart->getQuantityOf($product2));

        // add some more of second product
        $cart->add($product2, 1);
        $this->assertCount(2, $cart->getLineItems());
        $this->assertEquals(5, $cart->getQuantityOf($product1));
        $this->assertEquals(2, $cart->getQuantityOf($product2));

        // remove some of first product
        $cart->remove($product1, 1);
        $this->assertCount(2, $cart->getLineItems());
        $this->assertEquals(4, $cart->getQuantityOf($product1));
        $this->assertEquals(2, $cart->getQuantityOf($product2));

        // remove more than there was of the second product
        $cart->remove($product2, 1000);
        $this->assertCount(1, $cart->getLineItems());
        $this->assertEquals(4, $cart->getQuantityOf($product1));
        $this->assertEquals(0, $cart->getQuantityOf($product2));
    }

}