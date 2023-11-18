# CliLogger

Output errors, warnings, success or debug messages to ANSI terminal (color coded) or to dedicated log files (plain text). Automatically add timestamps and / or prefixes.

## Installation
The recommended way to install CliLogger is through
[Composer](https://getcomposer.org/).
```bash
composer require volkerschulz/cli-logger
```

## Usage
Minimal:
```php
use volkerschulz\CliLogger as log;

log::error('Error message');
log::success('Success message');
```

## Security

If you discover a security vulnerability within this package, please send an email to security@volkerschulz.de. All security vulnerabilities will be promptly addressed. Please do not disclose security-related issues publicly until a fix has been announced. 

## License

This package is made available under the MIT License (MIT). Please see [License File](LICENSE) for more information.
