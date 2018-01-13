# Pragmatic Cart

## Prerequisites

1. Get a command line PHP interpreter. In Ubuntu this can be installed by typing `sudo apt-get install php-cli`.
2. Generate autoloader: `lib/composer dump-autoload`.

## Running

Example use case is provided in file `example.php`. To run it simply enter `php example.php`.
Feel free to play with example file changing product and promo settings at will.

## Testing

1. Install PHPUnit: `lib/composer install`.
2. Run unit tests: `vendor/bin/phpunit`.
