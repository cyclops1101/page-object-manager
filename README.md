# Page Object Manager

### Install

```bash
composer require cyclops1101/page-object-manager
```

Then register the Nova tool in `app/Providers/NovaServiceProvider.php`:

```php
public function tools()
{
    return [
        \Cyclops1101\PageObjectManager\PageObject::make(),
    ];
}
```
