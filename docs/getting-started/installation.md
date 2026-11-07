# Installation

Part of the [atp-schema docs](../introduction.md#documentation). Installing the package and publishing its config and lexicons.

## Installing

```bash
composer require socialdept/atp-schema
```

The package registers itself with Laravel. It adds the `Schema` facade alias, the `schema()` helpers and the `schema:*` artisan commands.

> [!NOTE]
> Laravel's own database facade is also aliased as `Schema`. Import `SocialDept\AtpSchema\Facades\Schema` by its full name to be sure which one you get.

## Configuration

Publishing the config is optional. Every setting has a default:

```bash
php artisan vendor:publish --tag=atp-schema-config
```

This writes `config/atp-schema.php`. See [Configuration](configuration.md) for what each setting does.

## Lexicon files

The package bundles the JSON lexicons for the standard AT Protocol and Bluesky namespaces and reads them in place. To copy them into your app, for reference or to generate from them, publish them:

```bash
php artisan vendor:publish --tag=atp-lexicons
```

This copies them to `resources/lexicons`.
