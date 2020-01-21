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
Publish and run the migrations
```
php artisan vendor:publish --provider="Cyclops1101\PageObjectManager\PageServiceProvider" --tag="migrations"
php artisan migrate
```

In order to create a template run the following command
```
php artisan make:template AboutUs
```

Page and Block objects use any Nova Field you have installed.

Load your page/block in your controller using the name you set in the Nova admin and then you have access to all the data you've stored on the object

```$xslt
public function __invoke(Manager $page)
    {
        return view('about-us')
            ->with([
                'page' => $page->load('About Us'),
            ]);
    }
```