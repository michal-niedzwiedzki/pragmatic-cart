<?php

namespace Test\Epsi\PragmaticCart\Promo;

use \PHPUnit\Framework\TestCase;
use \Epsi\PragmaticCart\Promo\BulkDiscount;
use \Epsi\PragmaticCart\Checkout\Cart;
use \Epsi\PragmaticCart\Checkout\LineItem;
use \Epsi\PragmaticCart\Store\Product;

/**
 * Test of bulk discount promotion
 *
 * @coversDefaultClass \Epsi\PragmaticCart\Promo\BulkDiscount
 * @author Michał Rudnicki <michal@epsi.pl>
 */
class BulkDiscountTest extends TestCase {

    /**
     * @test
     * @covers ::__construct
     * @covers ::getDescription
     */
    public function constructor_initializes_description_and_getDescription_returns_it() {
        $target = new Product(1, 2, 3, 4);
        $promo = new BulkDiscount("bulk discount", $target, false);
        $this->assertEquals("bulk discount (3 for 4)", $promo->getDescription());
    }

    /**
     * @test
     * @covers ::getLineItemDiscount
     */
    public function getLineItemDiscount_returns_zero_on_product_mismatch() {
        $mismatch = new Product(2, 3, 4, 5);
        $target = new Product(1, 2, 3, 4);
        $promo = new BulkDiscount("bulk discount", $target, false);
        $regularItem = new LineItem($mismatch, 3, [$promo]);
        $discountedItem = new LineItem($target, 3, [$promo]);
        $this->assertEquals(0, $promo->getLineItemDiscount($regularItem));
        $this->assertGreaterThan(0, $promo->getLineItemDiscount($discountedItem));
    }

    /**
     * @test
     * @covers ::getLineItemDiscount
     */
    public function getLineItemDiscount_returns_zero_on_exclusive_promo_with_another_promo_on_line_item() {
        $target = new Product(1, 2, 3, 4);
        $promo1 = new BulkDiscount("bulk discount", $target, true);
        $promo2 = new BulkDiscount("another bulk discount, which is silly, but hey", $target, false);
        $item = new LineItem($target, 3, [$promo1, $promo2]);
        $this->assertEquals(0, $promo1->getLineItemDiscount($item));
        $this->assertGreaterThan(0, $promo2->getLineItemDiscount($item));
    }

    /**
     * @test
     * @covers ::getLineItemDiscount
     */
    public function getLineItemDiscount_returns_zero_on_quantity_below_threshold() {
        $target = new Product(1, 2, 3, 4);
        $promo = new BulkDiscount("bulk discount", $target, false);
        $regularItem = new LineItem($target, 2, [$promo]);
        $discountedItem = new LineItem($target, 3, [$promo]);
        $this->assertEquals(0, $promo->getLineItemDiscount($regularItem));
        $this->assertGreaterThan(0, $promo->getLineItemDiscount($discountedItem));
    }

    /**
     * @test
     * @covers ::getLineItemDiscount
     */
    public function getLineItemDiscount_returns_discount_when_all_conditions_met() {
        $target = new Product(1, 2, 3, 4);
        $promo = new BulkDiscount("bulk discount", $target, false);
        $this->assertEquals(0, $promo->getLineItemDiscount(new LineItem($target, 1, [$promo])));
        $this->assertEquals(0, $promo->getLineItemDiscount(new LineItem($target, 2, [$promo])));
        $this->assertEquals(2, $promo->getLineItemDiscount(new LineItem($target, 3, [$promo]))); // 3 for 4 (special) = 4, discount 2
        $this->assertEquals(2, $promo->getLineItemDiscount(new LineItem($target, 4, [$promo]))); // 3 for 4 (special) + 1 for 2 (regular) = 6, discount 2
        $this->assertEquals(2, $promo->getLineItemDiscount(new LineItem($target, 5, [$promo]))); // 3 for 4 (special) + 2 for 2 (regular) = 8, discount 2
        $this->assertEquals(4, $promo->getLineItemDiscount(new LineItem($target, 6, [$promo]))); // 6 for 8 (special) = 8, discount 4
        $this->assertEquals(4, $promo->getLineItemDiscount(new LineItem($target, 7, [$promo]))); // 6 for 8 (special) + 1 for 2 (regular) = 10, discount 4
    }

    /**
     * @test
     * @covers ::getCartDiscount
     */
    public function getCartDiscount_returns_zero() {
        $target = new Product(1, 2, 3, 4);
        $promo = new BulkDiscount("bulk discount", $target, false);
        $this->assertEquals(0, $promo->getCartDiscount(new Cart()));
    }

}