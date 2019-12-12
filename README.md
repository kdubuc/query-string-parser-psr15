# Query String Parser

If multiple fields of the same name exist in a query string, every other web processing language would read them into an array, but PHP silently overwrites them ([source](https://www.php.net/manual/en/function.parse-str.php#76792)). PSR-15 Middleware parse query string and keep duplicates in PSR7 URI Query.

## Install

Via Composer

``` bash
$ composer require kdubuc/query-string-parser
```

## Testing

``` bash
$ vendor/bin/phpunit tests/
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email kevindubuc62@gmail.com instead of using the issue tracker.

## Credits

- [Kévin DUBUC](https://github.com/kdubuc)
- [All Contributors](https://github.com/kdubuc/query-string-parser/graphs/contributors)

## License

The CeCILL-B License. Please see [License File](LICENSE.md) for more information.