# Utilizando o MarkHelp como biblioteca

Após a atualização do composer, basta instanciar o MarkHelper e usar, como no exemplo abaixo:

```php
include 'vendor/autoload.php';

$app = new MarkHelp('/meus/arquivos/markdown/');
$app->loadConfigFrom('/minhas/configurações/personalizadas/config.json');
$app->saveTo('/meu/site/html');
```

> Essa parte da documentação está sendo elaborada. Em breve mais informações sobre os parâmetros de imlementação.

## Sumário

-   [Início](index.md)
-   [Instalando](instalando.md)
-   [Utilizando em projetos PHP](utilizar-como-biblioteca.md)
-   [Utilizando no Terminal Linux](utilizar-no-terminal.md)
-   [Personalizando e Configurando](configuracoes.md)
-   [Quero ajudar o projeto](como-ajudar.md)
