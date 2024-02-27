# Laravel Nova Zadarma

## TODO
- tests
- webhook validation

## Webhooks

In Zadarma Integrations Notifications set PBX call webhook url to:

```
https://YOUR-DOMAIN.com/nova-vendor/webard/nova-zadarma/webhook/pbx-call
```

and enable all checkboxes.

Set events webhook url to:

```
https://YOUR-DOMAIN.com/nova-vendor/webard/nova-zadarma/webhook/event
```

and enable all checkboxes.

Add entry to `$except` property in `App\Http\Middleware\VerifyCsrfToken` class:
```
'nova-vendor/webard/nova-zadarma/webhook/*'
```

If you have `fruitcake/laravel-telescope-toolbar` installed, add this entry too:
```
'nova-vendor/webard/nova-zadarma/webhook/*'
```
to `ignore_paths` in `config/telescope-toolbar.php`