# Implementando projetos PHP

## 1. Instalando a biblioteca

Para utilizar o MarkHelp como biblioteca em um projeto PHP, é preciso fazer a instalação através do [composer](https://getcomposer.org/):

```bash
composer require ricardopedias/markhelp
composer update
```

Após a atualização do composer, basta instanciar o MarkHelper e usar:

```php
include 'vendor/autoload.php';

$app = new MarkHelp('/meus/arquivos/markdown/');
```


## 2. Uso básico

Após a instalação, basta instanciar o MarkHelper e usar, como no exemplo abaixo:

```php
include 'vendor/autoload.php';

$app = new MarkHelp('/caminho/ate/meu/projeto/markdown/');
$app->saveTo('/caminho/para/resultado/html/');
```

## 3. Definindo configurações

Para especificar um arquivo contendo as configurações, use o método `loadConfigFrom`:

```php
$app = new MarkHelp('/caminho/ate/meu/projeto/markdown/');
$app->loadConfigFrom('/minhas/configurações/personalizadas/config.json');
$app->saveTo('/caminho/para/resultado/html/');
```

Para setar parâmetros de configurações pontuais, use o método `config`:

```php
$app = new MarkHelp('/caminho/ate/meu/projeto/markdown/');
$app->config('project.name', 'Meu Projeto Legal');
$app->config('assets.logo.src', '/caminho/do/meu/logotipo.png');
$app->saveTo('/caminho/para/resultado/html/');
```

Para mais informações sobra as configurações disponíveis, configa [Personalizando e Configurando](configuracoes.md).

## Sumário

-   [Início](index.md)
-   [Implementando projetos PHP](utilizar-como-biblioteca.md)
-   [Utilizando no Terminal Linux](utilizar-no-terminal.md)
-   [Personalizando e Configurando](configuracoes.md)
-   [Quero ajudar o projeto](como-ajudar.md)
