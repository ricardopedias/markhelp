# Utilizando o MarkHelp como biblioteca

A utilização básica da bibliotecaApós a atualização do composer, basta instanciar o MarkHelper e usar:

```php
include 'vendor/autoload.php';

$app = new MarkHelp('/meus/arquivos/markdown/');
$app->loadConfigFrom('/minhas/configurações/personalizadas/config.json');
$app->saveTo('/meu/site/html');
```Essa parte está sendo elaborada.