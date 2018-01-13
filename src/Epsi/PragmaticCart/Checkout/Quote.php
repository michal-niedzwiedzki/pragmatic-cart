<?php

namespace \Epsi\PragmaticCart\Checkout;

interface Quote {

    public function getAmount();

    public function getDiscount();

    public function getTotal();

    public function getAvailablePromos();

    public function getApplicablePromos();

}