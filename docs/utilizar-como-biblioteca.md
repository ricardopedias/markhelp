# Utilizando o MarkHelp como biblioteca

Após a atualização do composer, basta instanciar o MarkHelper e usar, como no exemplo abaixo:

```php
include 'vendor/autoload.php';

$app = new MarkHelp('/meus/arquivos/markdown/');
$app->loadConfigFrom('/minhas/configurações/personalizadas/config.json');
$app->saveTo('/meu/site/html');
```

> Essa parte da documentação está sendo elaborada. Em breve mais informações sobre os parâmetros de configuração.

-   [Instalando](instalando.md)
-   [Utilizar como Biblioteca](utilizar-como-biblioteca.md)
-   [Utilizar no Terminal](utilizar-no-terminal.md)
-   [Ajudar o Projeto](como-ajudar.md)
