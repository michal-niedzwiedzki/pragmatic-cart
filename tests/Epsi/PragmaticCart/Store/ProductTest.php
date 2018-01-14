<?php

namespace Test\Epsi\PragmaticCart\Store;

use \PHPUnit\Framework\TestCase;
use \Epsi\PragmaticCart\Store\Product;

/**
 * Test of product
 *
 * @coversDefaultClass \Epsi\PragmaticCart\Store\Product
 * @author Michał Rudnicki <michal@epsi.pl>
 */
class ProductTest extends TestCase {

    /**
     * @test
     * @covers ::__construct
     * @covers ::getId
     * @covers ::getName
     * @covers ::getUnitsInBulk
     * @covers ::getPriceInBulk
     */
    public function constructor_initializes_product_properties() {
        $product = new Product("FOO", 2, 3, 4);
        $this->assertEquals("FOO", $product->getId());
        $this->assertEquals("FOO", $product->getName());
        $this->assertEquals(2, $product->getPrice());
        $this->assertEquals(3, $product->getUnitsInBulk());
        $this->assertEquals(4, $product->getPriceInBulk());
    }

    /**
     * @test
     * @covers ::import
     */
    public function import_returns_valid_product() {
        $product = Product::import([
            "name" => "FOO",
            "price" => 2,
            "unit" => 3,
            "specialPrice" => 4,
            "someIrrelevantNode" => "BAR",
        ]);
        $this->assertEquals("FOO", $product->getId());
        $this->assertEquals("FOO", $product->getName());
        $this->assertEquals(2, $product->getPrice());
        $this->assertEquals(3, $product->getUnitsInBulk());
        $this->assertEquals(4, $product->getPriceInBulk());
    }

    /**
     * @test
     * @covers ::export
     */
    public function export_returns_valid_array() {
        $product = new Product("FOO", 2, 3, 4);
        $p = $product->export();
        $expected = ["name" => "FOO", "price" => 2, "unit" => 3, "specialPrice" => 4];
        $actual = (new Product("FOO", 2, 3, 4))->export();
        $this->assertArraySubset($expected, $actual);
    }

}