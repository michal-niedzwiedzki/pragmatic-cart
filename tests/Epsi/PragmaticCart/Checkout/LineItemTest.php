<?php

namespace Test\Epsi\PragmaticCart\Checkout;

use \PHPUnit\Framework\TestCase;
use \Epsi\PragmaticCart\Store\Product;
use \Epsi\PragmaticCart\Checkout\LineItem;

/**
 * Test of line item
 *
 * @coversDefaultClass \Epsi\PragmaticCart\Checkout\LineItem
 * @author Michał Rudnicki <michal@epsi.pl>
 */
class LineItemTest extends TestCase {

    /**
     * @test
     * @covers ::__construct
     * @covers ::getProduct
     * @covers ::getQuantity
     * @covers ::getAvailablePromos
     */
    public function constructor_initializes_product_quantity_and_available_promos() {
        $promo = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $promos = [$promo, $promo];
        $product = new Product("FOO", 1, 2, 3);
        $item = new LineItem($product, 5, $promos);
        $this->assertSame($product, $item->getProduct());
        $this->assertEquals(5, $item->getQuantity());
        $this->assertSame($promos, $item->getAvailablePromos());
    }

    /**
     * @test
     * @covers ::modifyQuantityBy
     */
    public function modifyQuantityBy_does_the_math() {
        $product = new Product("FOO", 1, 2, 3);
        $item = new LineItem($product, 5, []);
        $this->assertEquals(5, $item->getQuantity());

        // test adding 0
        $this->assertSame($item, $item->modifyQuantityBy(0));
        $this->assertEquals(5, $item->getQuantity());

        // test adding positive int
        $item->modifyQuantityBy(3);
        $this->assertEquals(8, $item->getQuantity());

        // test subtracting an int
        $item->modifyQuantityBy(-5);
        $this->assertEquals(3, $item->getQuantity());

        // test going into negatives
        $item->modifyQuantityBy(-100);
        $this->assertEquals(0, $item->getQuantity());
    }

    /**
     * @test
     * @covers ::getAmount
     */
    public function getAmount_returns_price_times_quantity() {
        $product = new Product("FOO", 2, 3, 4);
        $item = new LineItem($product, 5, []);
        $this->assertEquals(10, $item->getAmount()); // price 2 * quantity 5
    }

    /**
     * @test
     * @covers ::getDiscount
     * @covers ::calculate
     * @covers ::getAvailablePromos
     * @covers ::getApplicablePromos
     */
    public function getDiscount_returns_zero_when_no_applicable_promos() {
        // mock promos
        $promo1 = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->setMethods(["getLineItemDiscount"])
            ->getMockForAbstractClass();
        $promo1->expects($this->once())
            ->method("getLineItemDiscount")
            ->will($this->returnValue(0)); // does not apply
        $promo2 = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->setMethods(["getLineItemDiscount"])
            ->getMockForAbstractClass();
        $promo2->expects($this->once())
            ->method("getLineItemDiscount")
            ->will($this->returnValue(0)); // does not apply

        // prepare line item
        $product = new Product("FOO", 2, 3, 4);
        $item = new LineItem($product, 5, [$promo1, $promo2]);

        // test if no discount
        $this->assertEquals(0, $item->getDiscount());

        // check if promos available but none applicable
        $this->assertCount(2, $item->getAvailablePromos());
        $this->assertCount(0, $item->getApplicablePromos());
    }

    /**
     * @test
     * @covers ::getDiscount
     * @covers ::calculate
     * @covers ::getAvailablePromos
     * @covers ::getApplicablePromos
     */
    public function getDiscount_returns_discount_when_promos_apply() {
        // mock promos
        $promo1 = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->setMethods(["getLineItemDiscount"])
            ->getMockForAbstractClass();
        $promo1->expects($this->once())
            ->method("getLineItemDiscount")
            ->will($this->returnValue(0)); // does not apply
        $promo2 = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->setMethods(["getLineItemDiscount"])
            ->getMockForAbstractClass();
        $promo2->expects($this->once())
            ->method("getLineItemDiscount")
            ->will($this->returnValue(1)); // discount 1
        $promo3 = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->setMethods(["getLineItemDiscount"])
            ->getMockForAbstractClass();
        $promo3->expects($this->once())
            ->method("getLineItemDiscount")
            ->will($this->returnValue(2)); // discount 2

        // prepare line item
        $product = new Product("FOO", 10, 3, 9);
        $item = new LineItem($product, 5, [$promo1, $promo2, $promo3]);

        // test if discount returned
        $this->assertEquals(3, $item->getDiscount());

        // check if promos
        $this->assertCount(3, $item->getAvailablePromos());
        $this->assertCount(2, $item->getApplicablePromos());
    }

    /**
     * @test
     * @covers ::getDiscount
     * @covers ::calculate
     */
    public function getDiscount_returns_discount_equal_amount_when_discount_greater_than_amount() {
        // mock promos
        $promo = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->setMethods(["getLineItemDiscount"])
            ->getMockForAbstractClass();
        $promo->expects($this->once())
            ->method("getLineItemDiscount")
            ->will($this->returnValue(500)); // discount 500

        // prepare line item
        $product = new Product("FOO", 10, 3, 25); // price 10
        $item = new LineItem($product, 1, [$promo]);

        // test if discount returned and not greater than amount
        $this->assertEquals(10, $item->getDiscount());
    }

    /**
     * @test
     * @covers ::getAmount
     * @covers ::getDiscount
     * @covers ::getTotal
     * @covers ::calculate
     */
    public function getTotal_returns_amount_reduced_by_discount() {
        // mock promos
        $promo = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->setMethods(["getLineItemDiscount"])
            ->getMockForAbstractClass();
        $promo->expects($this->once())
            ->method("getLineItemDiscount")
            ->will($this->returnValue(1)); // discount 1

        // prepare line item
        $product = new Product("FOO", 10, 3, 25); // price 10
        $item = new LineItem($product, 2, [$promo]);

        // test amount, discount, and total
        $this->assertEquals(20, $item->getAmount());
        $this->assertEquals(1, $item->getDiscount());
        $this->assertEquals(19, $item->getTotal());
    }

}