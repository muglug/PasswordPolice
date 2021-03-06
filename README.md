# PasswordPolice

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Password policy enforcement made easy.

## Install

Via Composer

``` bash
composer require stadly/password-police
```

## Usage

``` php
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\WordFormatter\LeetDecoder;
use Stadly\PasswordPolice\WordFormatter\LowerCaseConverter;
use Stadly\PasswordPolice\WordFormatter\UpperCaseConverter;
use Stadly\PasswordPolice\HashFunction\PasswordHasher;
use Stadly\PasswordPolice\Rule\DigitRule;
use Stadly\PasswordPolice\Rule\DictionaryRule;
use Stadly\PasswordPolice\Rule\GuessableDataRule;
use Stadly\PasswordPolice\Rule\HaveIBeenPwnedRule;
use Stadly\PasswordPolice\Rule\LengthRule;
use Stadly\PasswordPolice\Rule\LowerCaseRule;
use Stadly\PasswordPolice\Rule\NoReuseRule;
use Stadly\PasswordPolice\Rule\UpperCaseRule;

$policy = new Policy();
$policy->addRules(new LengthRule(8));                     // Password must be at least 8 characters long.
$policy->addRules(new LowerCaseRule());                   // Password must contain lower case letters.
$policy->addRules(new UpperCaseRule());                   // Password must contain upper case letters.
$policy->addRules(new DigitRule());                       // Password must contain digits.
$policy->addRules(new GuessableDataRule(['company']));    // Password must not contain data that is easy to guess.
$policy->addRules(new HaveIBeenPwnedRule());              // Password must not be exposed in data breaches.
$policy->addRules(new NoReuseRule(new PasswordHasher())); // Password must not have been used earlier.
$pspell = Pspell::fromLocale('en', [new LowerCaseConverter(), new UpperCaseConverter()]);
$dictionary = new DictionaryRule($pspell, [new LeetDecoder()]);
$policy->addRules($dictionary));                          // Password must not contain dictionary words.

$validationErrors = $policy->validate('password');
if (empty($validationErrors)) {
    // The password is in compliance with the policy.
} else {
    // The password is not incompliance with the policy.
    // Use the array of validation errors to show appropriate messages to the user.
}


// Specify additional data that is easy to guess for this password.
$guessableData = [
    'first name',
    'spouse',
    new DateTime('birthday'),
];
$validationErrors = $policy->validate(new Password('password', $guessableData));


// Specify former passwords that cannot be reused.
$formerPasswords = [
    new FormerPassword('hash of old password', new DateTimeImmutable('2018-11-30')),
    new FormerPassword('hash of even older password', new DateTimeImmutable('2010-08-23')),
];
$validationErrors = $policy->validate(new Password('password', [], $formerPasswords));
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email magnar@myrtveit.com instead of using the issue tracker.

## Credits

- [Magnar Ovedal Myrtveit][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/stadly/password-police.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Stadly/PasswordPolice/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/Stadly/PasswordPolice.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Stadly/PasswordPolice.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/stadly/password-police.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/stadly/password-police
[link-travis]: https://travis-ci.org/Stadly/PasswordPolice
[link-scrutinizer]: https://scrutinizer-ci.com/g/Stadly/PasswordPolice/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Stadly/PasswordPolice
[link-downloads]: https://packagist.org/packages/stadly/password-police
[link-author]: https://github.com/Stadly
[link-contributors]: ../../contributors
