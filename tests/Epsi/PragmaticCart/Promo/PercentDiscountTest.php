<?php

namespace Test\Epsi\PragmaticCart\Promo;

use \PHPUnit\Framework\TestCase;
use \Epsi\PragmaticCart\Promo\PercentDiscount;
use \Epsi\PragmaticCart\Checkout\Cart;
use \Epsi\PragmaticCart\Checkout\LineItem;
use \Epsi\PragmaticCart\Store\Product;

/**
 * Test of percent discount promotion
 *
 * @coversDefaultClass \Epsi\PragmaticCart\Promo\PercentDiscount
 * @author Michał Rudnicki <michal@epsi.pl>
 */
class PercentDiscountTest extends TestCase {

    /**
     * @test
     * @covers ::__construct
     * @covers ::getDescription
     */
    public function constructor_initializes_description_and_getDescription_returns_it() {
        $promo = new PercentDiscount("percent discount", 100, 10);
        $this->assertEquals("percent discount (10% off)", $promo->getDescription());
    }

    /**
     * @test
     * @covers ::getLineItemDiscount
     */
    public function getLineItemDiscount_returns_zero() {
        $product = new Product("A", 3, 4, 5);
        $promo = new PercentDiscount("percent discount", 100, 10);
        $item = new LineItem($product, 1, [$promo]);
        $this->assertEquals(0, $promo->getLineItemDiscount($item));
    }

    /**
     * @test
     * @covers ::getCartDiscount
     */
    public function getCartDiscount_returns_zero_on_below_threshold() {
        $product = new Product("A", 2, 3, 4);
        $promo = new PercentDiscount("percent discount", 100, 10);
        $cart = new Cart([$promo]);
        $cart->add($product, 1);
        $this->assertEquals(0, $promo->getCartDiscount($cart));
    }

    /**
     * @test
     * @covers ::getCartDiscount
     */
    public function getCartDiscount_returns_discount_at_and_above_threshold() {
        $product = new Product("A", 2, 3, 4);
        $promo = new PercentDiscount("percent discount", 100, 10);
        $cart = new Cart([$promo]);

        // check discount at threshold level
        $cart->add($product, 50); // 50 item * 2 price = 100
        $this->assertEquals(10, $promo->getCartDiscount($cart));

        // check discount above threshold
        $cart->add($product, 5); // 100 price already in cart + 5 items * 2 price = 110
        $this->assertEquals(11, $promo->getCartDiscount($cart));

    }

}