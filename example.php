<?php

require_once "vendor/composer/autoload.php";

use \Epsi\PragmaticCart\Store\Catalog;
use \Epsi\PragmaticCart\Store\Product;
use \Epsi\PragmaticCart\Checkout\Cart;
use \Epsi\PragmaticCart\Checkout\Quote;
use \Epsi\PragmaticCart\Promo\BulkDiscount;
use \Epsi\PragmaticCart\Promo\BundleDiscount;
use \Epsi\PragmaticCart\Promo\PercentDiscount;

// prepare catalog
$catalog = Catalog::getInstance();
$catalog->load("var/catalog.json");

// create cart and register available promotions
$cart = new Cart(md5("michal@epsi.pl"));
$promos = [
    new BulkDiscount("Bulk discount", $catalog->getProductById("A"), false));
    new BulkDiscount("Bulk discount", $catalog->getProductById("B"), false));
    new BulkDiscount("Bulk discount", $catalog->getProductById("C"), false));
    new BulkDiscount("Bulk discount", $catalog->getProductById("D"), false));
    new BundleDiscount("2 items bundle", [$catalog->getProductById("A"), $catalog->getProductById("B")], 10);
    new BundleDiscount("4 items bundle", [$catalog->getProductById("A"), $catalog->getProductById("B"), $catalog->getProductById("C"), $catalog->getProductById("D")], 30);
    new PercentDiscount("Heavy cart discount", 600, 10));
    // TODO: move manual promo configuration to a config file
];
foreach ($promos as $promo) {
    $cart->registerPromo($promo);
}

// throw some products into cart
$cart->add($catalog->getProductById("A"), 20);
$cart->add($catalog->getProductById("B"), 30);

// get a quote from cart and print recepit
$quote = $cart->getQuote();
print($quote->getReceipt());