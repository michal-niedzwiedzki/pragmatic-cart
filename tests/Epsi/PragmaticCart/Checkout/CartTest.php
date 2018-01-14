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
        $promo = $this->getMockBuilder('\Epsi\PragmaticCart\Promo\Promo')
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
        $promo = $this->getMockBuilder('\Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $promos = [$promo, $promo];
        $cart = new Cart($promos);
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

    /**
     * @test
     * @covers ::add
     * @covers ::getLineItems
     */
    public function add_passes_down_product_quantity_and_available_promos() {
        $promo = $this->getMockBuilder('\Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $promos = [$promo, $promo];
        $product = new Product("A", 1, 2, 3);
        $cart = new Cart($promos);
        $cart->add($product, 5);

        // test if values on line item match
        $items = $cart->getLineItems();
        $this->assertCount(1, $items);

        // verify the contents of line item
        $this->assertSame($product, $items["A"]->getProduct());
        $this->assertEquals(5, $items["A"]->getQuantity());
        $this->assertSame($promos, $items["A"]->getAvailablePromos());
    }

    /**
     * @test
     * @covers ::getAmount
     * @covers ::calculate
     */
    public function getAmount_returns_price_times_quantity_of_products() {
        $product1 = new Product("A", 1, 2, 3);
        $product2 = new Product("B", 2, 3, 4);
        $cart = new Cart();
        $cart->add($product1, 2);
        $cart->add($product2, 3);

        // test getAmount
        $this->assertEquals(2 * 1 + 3 * 2, $cart->getAmount());
    }

    /**
     * @test
     * @covers ::getDiscount
     * @covers ::getTotal
     * @covers ::calculate
     * @covers ::getApplicablePromos
     */
    public function getDiscount_getTotal_do_the_math_getApplicablePromos_returns_promos_that_apply() {
        $promo1 = $this->getMockBuilder('\Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->setMethods(["getLineItemDiscount", "getCartDiscount"])
            ->getMockForAbstractClass();
        $promo1->expects($this->once())
            ->method("getLineItemDiscount")
            ->will($this->returnValue(0)); // no line item discount
        $promo1->expects($this->once())
            ->method("getCartDiscount")
            ->will($this->returnValue(0)); // no cart discount
        $promo2 = $this->getMockBuilder('\Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->setMethods(["getLineItemDiscount", "getCartDiscount"])
            ->getMockForAbstractClass();
        $promo2->expects($this->once())
            ->method("getLineItemDiscount")
            ->will($this->returnValue(0)); // no line item discount
        $promo2->expects($this->once())
            ->method("getCartDiscount")
            ->will($this->returnValue(2)); // apply discount 2 on a cart

        $product = new Product("A", 5, 2, 9);
        $cart = new Cart([$promo1, $promo2]);
        $cart->add($product, 5);

        // test amount, discount, and total
        $this->assertEquals(25, $cart->getAmount());
        $this->assertEquals(2, $cart->getDiscount());
        $this->assertEquals(23, $cart->getTotal());

        // test applicable promos
        $applicable = $cart->getApplicablePromos();
        $this->assertCount(1, $applicable);
        $this->assertSame($promo2, $applicable[0]);
    }

}