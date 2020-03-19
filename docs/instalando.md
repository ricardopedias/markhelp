# Instalando o MarkHelp

## Instalar como biblioteca para projeto PHP

Para utilizar o MarkHelp em qualquer projeto PHP, basta instalar 
o pacote de software usando o composer:

```bash
composer require ricardopedias/markhelp
composer update
```

Após a atualização do composer, basta instanciar o MarkHelper e usar:

```php
include 'vendor/autoload.php';

$app = new MarkHelp('/meus/arquivos/markdown/');
```

Para mais informações, acesse [Implementando projetos PHP](utilizar-como-biblioteca.md).

## Instalar como comando de terminal em ambientes Linux (baseados em Debian)

Para utilizar o MarkHelp como um comando dentro de um sistema operacional 
baseado em Debian Linux, basta efetuar os seguintes passos:

```bash
git clone https://github.com/ricardopedias/markhelp.git
cd markhelp
./make-deb-package.sh
```

Um pacote chamado markhelp_9.9.9_all.deb será gerado no mesmo diretório. 
Basta instalar este pacote com o gerenciador de pacotes de sua preferência 
e começar a usar o MarkHelp em sua distribuição.

Para mais informações, acesse [Utilizando no Terminal Linux](utilizar-no-terminal.md).

## Sumário

-   [Início](index.md)
-   [Instalando o MarkHelp](instalando.md)
-   [Implementando projetos PHP](utilizar-como-biblioteca.md)
-   [Utilizando no Terminal Linux](utilizar-no-terminal.md)
-   [Personalizando e Configurando](configuracoes.md)
-   [Quero ajudar o projeto](como-ajudar.md)

