<?php

namespace Test\Epsi\PragmaticCart\Store;

use \PHPUnit\Framework\TestCase;
use \Epsi\PragmaticCart\Store\Catalog;
use \Epsi\PragmaticCart\Store\Product;

/**
 * Test of catalog
 *
 * @coversDefaultClass \Epsi\PragmaticCart\Store\Catalog
 * @author Michał Rudnicki <michal@epsi.pl>
 */
class CatalogTest extends TestCase {

    /**
     * @test
     * @covers ::load
     * @covers ::getProducts
     * @covers ::getProductById
     */
    public function load_and_verify_with_getProducts_and_getProductById() {
        $catalog = new Catalog();
        $catalog->load("var/catalog.json");
        $products = $catalog->getProducts();
        $this->assertCount(4, $products);
        $this->assertEquals("A", $catalog->getProductById("A")->getId());
        $this->assertEquals("B", $catalog->getProductById("B")->getId());
        $this->assertEquals("C", $catalog->getProductById("C")->getId());
        $this->assertEquals("D", $catalog->getProductById("D")->getId());
    }

    /**
     * @test
     * @covers ::load
     * @expectedException \Epsi\PragmaticCart\Store\Exception
     * @expectedExceptionCode -1
     */
    public function load_throws_on_catalog_file_not_found() {
        (new Catalog())->load("/some-nonexistent-file.json");
    }

    /**
     * @test
     * @covers ::getProductById
     * @expectedException \Epsi\PragmaticCart\Store\Exception
     * @expectedExceptionCode -2
     */
    public function getProductById_throws_on_unknown_product() {
        (new Catalog())->getProductById("FOO");
    }

}