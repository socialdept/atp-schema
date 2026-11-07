[![Schema Header](./header.png)](https://github.com/socialdept/atp-schema)

<h3 align="center">
    Parse, validate, and transform AT Protocol Lexicon schemas in Laravel.
</h3>

<p align="center">
    <br>
    <a href="https://packagist.org/packages/socialdept/atp-schema" title="Latest Version on Packagist"><img src="https://img.shields.io/packagist/v/socialdept/atp-schema.svg?style=flat-square"></a>
    <a href="https://packagist.org/packages/socialdept/atp-schema" title="Total Downloads"><img src="https://img.shields.io/packagist/dt/socialdept/atp-schema.svg?style=flat-square"></a>
    <a href="LICENSE" title="Software License"><img src="https://img.shields.io/github/license/socialdept/atp-schema?style=flat-square"></a>
</p>

---

This package loads AT Protocol lexicons into a Laravel app, validates records against them, ships PHP classes for the standard AT Protocol and Bluesky lexicons, generates classes for any other lexicon, and gives you commands to create, lint and check lexicons of your own before you publish them. To learn all about it, head over to [the documentation](https://socialde.pt/docs/atp-schema).

Here are a few short examples of what you can do. Validate a record against a lexicon:

```php
use SocialDept\AtpSchema\Facades\Schema;

Schema::validate('app.bsky.feed.post', [
    'text' => 'Hello, AT Protocol!',
    'createdAt' => '2024-01-01T12:00:00Z',
]); // true
```

Build records with the bundled classes:

```php
use SocialDept\AtpSchema\Generated\App\Bsky\Feed\Post;

$post = new Post(text: 'Hello ATP!', createdAt: now());

$post->toRecord(); // ['text' => 'Hello ATP!', 'createdAt' => '...', '$type' => 'app.bsky.feed.post']
```

Generate classes for any lexicon:

```bash
php artisan schema:generate app.bsky.feed.post --with-dependencies
```

Write lexicons of your own, and check them before you publish:

```bash
php artisan schema:new record com.example.thing
php artisan schema:lint
php artisan schema:breaking
```

## Documentation

You'll find the documentation on [https://socialde.pt/docs/atp-schema](https://socialde.pt/docs/atp-schema).

Find yourself stuck using the package? Found a bug? Do you have general questions or suggestions for improving it? Feel free to [create an issue on GitHub](https://github.com/socialdept/atp-schema/issues).

## Testing

```bash
vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
