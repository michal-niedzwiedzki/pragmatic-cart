<?php

namespace Test\Epsi\PragmaticCart\Promo;

use \PHPUnit\Framework\TestCase;
use \Epsi\PragmaticCart\Checkout\Cart;
use \Epsi\PragmaticCart\Checkout\LineItem;
use \Epsi\PragmaticCart\Store\Product;

/**
 * Test of abstract promotion
 *
 * @coversDefaultClass \Epsi\PragmaticCart\Promo\Promo
 * @author Michał Rudnicki <michal@epsi.pl>
 */
class PromoTest extends TestCase {

    /**
     * @test
     * @covers ::__construct
     * @covers ::getDescription
     */
    public function constructor_initializes_description_and_getDescription_returns_it() {
        eval('namespace Test\Epsi\PragmaticCart\Promo; class DummyPromo extends \Epsi\PragmaticCart\Promo\Promo { }');
        $promo = new DummyPromo("dummy description");
        $this->assertSame("dummy description", $promo->getDescription());
    }

    /**
     * @test
     * @covers ::getDescription
     */
    public function getDescription_returns_empty_string_when_not_initialized_in_constructor() {
        $promo = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->assertSame("", $promo->getDescription());
    }

    /**
     * @test
     * @covers ::getLineItemDiscount
     */
    public function getLineItemDiscount_returns_zero() {
        $promo = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->assertSame(0, $promo->getLineItemDiscount(new LineItem(new Product(0, 0, 0, 0), 1, [])));
    }

    /**
     * @test
     * @covers ::getCartDiscount
     */
    public function getCartDiscount_returns_zero() {
        $promo = $this->getMockBuilder('Epsi\PragmaticCart\Promo\Promo')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->assertSame(0, $promo->getCartDiscount(new Cart()));
    }

}