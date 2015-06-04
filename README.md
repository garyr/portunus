[![Build Status](https://secure.travis-ci.org/garyr/portunus.png)](http://travis-ci.org/garyr/portunus)

Portunus - The God of Keys
=========

A library for storing encrypted secrets

## Install

```javascript
{
    "require": {
        "garyr/portunus": "1.0.*"
    },
    "scripts": {
        "post-update-cmd": [
            "Portunus\\Console\\Composer::postUpdate"
        ],
        "post-install-cmd": [
            "Portunus\\Console\\Composer::postInstall"
        ]
    }
}
```

## Portunus Safe

Portunus Safes can be synonymous with application environments (e.g. 'dev', 'test', 'prod', etc).

Safes and secrets are stored in an sqlite DB (defaults to ```./data``` dir in the parent dir of ```vendor-dir``` in your app). This path
filename can be customized using composer "extra" values.

```
{
    "extra": {
       "portunus-data-dir": "data",
        "portunus-db-name": "portunus.sqlite"
    }
}
```

#### Creating a Safe
```
$ ./vendor/bin/portunus safe:create dev

Creating safe 'dev'... DONE

PLEASE STORE PRIVATE KEY (CANNOT BE RECOVERED)
-----BEGIN RSA PRIVATE KEY-----
MIICXgIBAAKBgQDNbnPVippiJucJ/Ikb0TpxhZXi58x99Mw/vAHhG5Og9HaLtdRp
...
-----END RSA PRIVATE KEY-----

```
**Important:** Please store the private key for later use. The private key will be required at run-time for
decrypting of all secrets. Portunus does not provide a mechanism for storing or transferring private keys.

#### List Safes
```
$ ./vendor/bin/portunus safe:list

+-----------+----------------------+-----------+---------------------+---------------------+
| Safe Name | Signature            | # Secrets | Created             | Updated             |
+-----------+----------------------+-----------+---------------------+---------------------+
| dev       | b7f67d9ea53c0d8c6... | 12        | 2015-05-07 16:30:46 | 2015-05-07 16:30:46 |
| test      | a55dbfe5222125270... | 12        | 2015-05-07 16:30:49 | 2015-05-07 16:30:49 |
| prod      | a87b4d977d7bcfe75... | 12        | 2015-05-07 16:30:52 | 2015-05-07 16:30:52 |
+-----------+----------------------+-----------+---------------------+---------------------+
```

## Storing Secrets

#### Store a secret Key:Value pair
```
$ ./vendor/bin/portunus secret:store dev foo bar

Using safe 'dev'...

Creating secret 'foo'... DONE

```
This command will encrypt the string 'bar' under the reference of 'foo' in the safe 'dev'

#### List stored secrets
```
$ ./bin/portunus secret:list dev

+-----------+-------------------------+--------+---------------------+---------------------+
| Key Name  | Signature               | Length | Created             | Updated             |
+-----------+-------------------------+--------+---------------------+---------------------+
| foo       | fe1cbb60a0249ecbd3f2... | 128    | 2015-05-07 16:32:03 | 2015-05-07 16:32:03 |
| foo.foo   | 847b80314a68c84ab0c9... | 128    | 2015-05-07 16:33:21 | 2015-05-07 16:33:21 |
| foo3      | 0e0da8e1ef532f19120e... | 128    | 2015-05-07 16:33:41 | 2015-05-07 16:33:41 |
| foofoo    | 998d5692a9f162e07937... | 128    | 2015-05-07 16:33:18 | 2015-05-07 16:33:18 |
+-----------+-------------------------+--------+---------------------+---------------------+

```

#### Retrieving Secrets in your application

```php
// callback to deliver private key
$callback = function($safeName) {
    // this should return your private key
    return $myPrivateKeyBytes;
};

$Agent = new Portunus\Application\Agent();
$Agent->setSafe('dev');
$Agent->setPrivateKeyCallback($callback);

// retrieve decrypted value 'bar'
$value = $Agent->getKey('foo');
```

## Testing

Basic PHPUnit Test Coverage
```
$ cd path/to/Portunus/
$ composer install
$ phpunit
```
