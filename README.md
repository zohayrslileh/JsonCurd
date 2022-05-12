# JsonCurd

> **Sample project for the experiment**

* 🗁 myProject
    * 🗁 JsonCurd
        * 🗎 JsonCurd.php
    * 🗎 myFile.json
    * 🗎 script.php
    

* myFile.json
```json
{
    "users": [
        {
            "name": "Zohayr SLILEH",
            "email": "zohayrslileh@gmail.com",
            "age": 22,
            "favoriteColor": "blue"
        },
        {
            "name": "Lonnie WILSON",
            "age": 25,
            "favoriteColor": "green"
        }
    ],
    "settings": {
        "addons": {
            "firstAddon": false
        }
    }
}
```

* script.php

```php
require_once __DIR__ . '/JsonCurd/JsonCurd.php';

$Json = JsonCurd::open( __DIR__ . '/myFile.json' )->go('/users/0');

print_r( $Json->read() );

```

- Output

```json
{
    "name": "Zohayr SLILEH",
    "email": "zohayrslileh@gmail.com",
    "age": 22,
    "favoriteColor": "blue"
}
```




> **Get started**


* Open json file
```php
$Json = JsonCurd::open( __DIR__ . '/myFile.json' );
```

* Create json file and opened
```php
$Json = JsonCurd::create( __DIR__ . '/myFile.json' );
```


> **CURD**
* Go to a specific part of the file
```php
$Json = $Json->go('/settings/addons');
```
Or
```php
$Json = $Json->go('/settings')->go('addons');
```

* Read
```php
$addons = $Json->read();
echo $addons->firstAddon;
```
- Output
```ssh
false
```

* Update
```php
$Json->set([
    'secondAddon' => true,
]);

print_r( $Json->read() );
```
- Output
```json
{
    "firstAddon": false,
    "secondAddon": true
}
```
