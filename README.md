# Decisions

Define values in a JSON file and they will override corresponding option values from the database. ALWAYS.

## Requirements

- PHP 5.3
  - Sorry
  - Not sorry.

## Usage

You REALLY should use this as a must-use plugin, but that would require some know-how and extra setup. If you don't know how MU plugins work or how to get this set up as one, maybe you don't really need this plugin? Or you might want to better understand why you need it?

By default, this will look for a configuration file in `ABSPATH . '.decisions.json'`. You can change this location in your config file thusly:

```php
define( 'JPB\Decisions\DECISIONS', 'wherever/you/want' );
```

Any options you want to be set by default go into the `standard` hash:

```json
{
  "standard": {
    "blogname": "Super Serial Blog Name",
    "admin_email": "john@example.com"
  }
}
```

In a multisite network, you can add or override standard options with a site specific hash. The site options should be keyed by site domain + site path with slashes and protocol trimmed (so `http://example.com/subsite/` becomes `example.com/subsite`). Site-specific options go under the `sites` key:

```json
{
  "sites": {
    "example.com/subsite": {
      "admin_email": "jane@example.com"
    },
    "something-else.com/multinetwork": {
      "omgwat": "Still works"
    }
  }
}
```

In multisite you can also add network-wide options using the `network` hash:

```json
{
  "network": {
    "auth_policy": "Gandalf"
  }
}
```

JSON gets decoded as associative arrays rather than objects. If you need an object, you can serialize the data and store the serialized string directly. All values get passed through `maybe_unserialize` before use.

## Qs I Think Will Be FA'ed

#### JSON? Y U NO USE YAML/RAML/XML/LOLSPEAK?

PHP has core JSON decoding. Ain't nobody got time for userland libraries.

#### What about options fetched before this plugin runs?

I can't really help you there. That falls well outside the 80/20 rule. Maybe set the DB value directly and then short-circuit any attempt to update that key?

#### Do you?

I did.

## License

[MIT](https://github.com/johnpbloch/decisions/blob/master/LICENSE)
