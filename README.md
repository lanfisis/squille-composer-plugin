# Squille Composer Plugin

This Composer plugin detect Squille plugins into Composer packages and generate a pluging reposity class available in project.

## Register a plugin

To register a Squille plugin, you need to add informations into extra part of your `composer.json` file.

```json
{
    "extra": {
        "squille-plugin": ["Foo\\Bar\\Plugin\\BazPlugin"]
    }
}
```

## Get plugin list

Into your lib you can access to plugin list like this

```php
<?php
include 'vendor/autoload.php';
$plugins = Burdz\Squille\Composer\PluginRepository::getAll();
```

## Testing

``` bash
$ composer test
```
