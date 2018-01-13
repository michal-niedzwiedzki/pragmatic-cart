# Pragmatic Cart

## Prerequisites

1. Get a command line PHP interpreter. In Ubuntu this can be installed by typing `sudo apt-get install php-cli`.
2. Generate autoloader: `lib/composer dump-autoload`.

## Running

Example use case is provided in file _example.php_. To run it simply enter `php example.php`.

The script will produce a receipt in JSON format. Top level nodes are:
- _items_ - for line items in cart, each item can have promotions that apply to it,
- _promos_ - for promotions that apply to entire cart,
- _amount_ - partial total not including cart promotions,
- _discount_ - discount as a result of applicable promotions,
- _total_ - final amount to pay.

Feel free to play with example file changing product and promo settings at will.

## Testing

1. Install PHPUnit: `lib/composer install`.
2. Run unit tests: `vendor/bin/phpunit`.
