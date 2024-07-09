# Fakerphp Picsum Images

[![Latest Version on Packagist](https://img.shields.io/packagist/v/toneflix-code/fakerphp-lorem-toneflix.svg?style=flat-square)](https://packagist.org/packages/toneflix-code/fakerphp-lorem-toneflix)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/toneflix-code/fakerphp-lorem-toneflix/run-tests.yml?branch=main&style=flat-square)](https://github.com/toneflix-code/fakerphp-lorem-toneflix/actions?query=workflow%3ATests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/toneflix-code/fakerphp-lorem-toneflix.svg?style=flat-square)](https://packagist.org/packages/toneflix-code/fakerphp-lorem-toneflix)

## Introduction

Alternative image provider for [fakerphp](https://github.com/fakerphp/faker) using [lorem.toneflix.com.ng](https://lorem.toneflix.com.ng)
 
## Installation

You can install the package via composer in dev dependency section:

```bash
composer require --dev toneflix-code/fakerphp-lorem-toneflix
```

## Usage

```php
$faker = \Faker\Factory::create();
$faker->addProvider(new \ToneflixCode\FakerLoremToneflix\FakerLoremToneflixProvider($faker));

// return a string that contains a url like 'https://lorem.toneflix.com.ng/images/avatar?w=800&h=600'
$faker->imageUrl(width: 800, height: 600, category: 'avatar'); 

// download a properly sized image from lorem toneflix into a file with a file path like '/tmp/13b73edae8443990be1aa8f1a483bc27.jpg'
$filePath= $faker->image(dir: '/tmp', width: 640, height: 480);
```

Also, there are some more options :
- alternative webp format
- effects (grayscale, blurry)
- returning a specific photo based on an id instead of a random one (ex: https://lorem.toneflix.com.ng/images/image/00020)

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Toneflix Code](https://github.com/toneflix)
- [Legacy ](https://github.com/3m1n3nc3)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
