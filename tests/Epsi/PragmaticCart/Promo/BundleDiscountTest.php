<?php

namespace Test\Epsi\PragmaticCart\Promo;

use \PHPUnit\Framework\TestCase;
use \Epsi\PragmaticCart\Promo\BundleDiscount;
use \Epsi\PragmaticCart\Checkout\Cart;
use \Epsi\PragmaticCart\Checkout\LineItem;
use \Epsi\PragmaticCart\Store\Product;

/**
 * Test of bundle discount promotion
 *
 * @coversDefaultClass \Epsi\PragmaticCart\Promo\BundleDiscount
 * @author Michał Rudnicki <michal@epsi.pl>
 */
class BundleDiscountTest extends TestCase {

    /**
     * @test
     * @covers ::__construct
     * @covers ::getDescription
     */
    public function constructor_initializes_description_and_getDescription_returns_it() {
        $product1 = new Product("A", 2, 3, 4);
        $product2 = new Product("B", 3, 4, 5);
        $promo = new BundleDiscount("bundle discount", [$product1, $product2], 1);
        $this->assertEquals("bundle discount (A+B for 1 less)", $promo->getDescription());
    }

    /**
     * @test
     * @covers ::getLineItemDiscount
     */
    public function getLineItemDiscount_returns_zero() {
        $product1 = new Product("A", 2, 3, 4);
        $product2 = new Product("B", 3, 4, 5);
        $promo = new BundleDiscount("bundle discount", [$product1, $product2], 1);
        $item = new LineItem($product1, 1, [$promo]);
        $this->assertEquals(0, $promo->getLineItemDiscount($item));
    }

    /**
     * @test
     * @covers ::getCartDiscount
     */
    public function getCartDiscount_returns_zero_on_incomplete_bundle() {
        $product1 = new Product("A", 2, 3, 4);
        $product2 = new Product("B", 3, 4, 5);
        $promo = new BundleDiscount("bundle discount", [$product1, $product2], 1);
        $cart = new Cart([$promo]);

        // check for empty cart
        $this->assertEquals(0, $promo->getCartDiscount($cart));

        // check for incomplete bundle
        $cart->add($product1, 1);
        $this->assertEquals(0, $promo->getCartDiscount($cart));

        // check for incomplete bundle the other way around
        $cart->remove($product1, 1);
        $cart->add($product2, 1);
        $this->assertEquals(0, $promo->getCartDiscount($cart));
    }

    /**
     * @test
     * @covers ::getCartDiscount
     */
    public function getCartDiscount_returns_discount_on_complete_bundle() {
        $product1 = new Product("A", 2, 3, 4);
        $product2 = new Product("B", 3, 4, 5);
        $product3 = new Product("C", 4, 5, 6);
        $promo = new BundleDiscount("bundle discount", [$product1, $product2], 1);
        $cart = new Cart([$promo]);
        $cart->add($product1, 1);
        $cart->add($product2, 1);

        // check for complete bundle
        $this->assertEquals(1, $promo->getCartDiscount($cart));

        // check for complete bundle plus odd bundle product
        $cart->add($product1, 1);
        $this->assertEquals(1, $promo->getCartDiscount($cart));

        // check for 2 complete bundles
        $cart->add($product2, 1);
        $this->assertEquals(2, $promo->getCartDiscount($cart));
    }

}