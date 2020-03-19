# Utilizando o MarkHelp no terminal

O MarkHelp foi escrito para funcionar em sistemas Linux. Por isso, o projeto 
acompanha um script para geração de pacotes Debian. 
Veja mais sobre isso [clicando aqui](instalando.md#em-ambiente-linux).

Após a correta instalação, o terminal possíbilita uma utilização global do MarkHelp,
sem a necessidade de programar. Isso é muito útil para quando a necessidade é apenas
gerar uma documentação HTML e disponibilizá-la manualmente em algum canal na internet.

> Obs: para automatizar o processo de criação de documentações, veja [Implementando projetos PHP](utilizar-como-biblioteca.md).

## 1. Uso básico

Dentro do terminal, basta digitar o comando markhelp --help para exibir a lista de ajuda.

```bash
markhelp --help
```

O uso mais simples é:

```bash
cd projeto/markdown
markhelp -o ../../caminho/para/resultado/html/
```

Uma forma mais declarativa é útil para executar fora do diretório do projeto:

```bash
markhelp -i /caminho/ate/meu/projeto/markdown/ -o /caminho/para/resultado/html/
```

O resultado será parecido com o mostrado abaixo:

```bash
---------------------------------------------------
MarkHelp 0.6.1
---------------------------------------------------
Reading from: /home/ricardo/caminho/ate/meu/projeto/markdown/
Saving in /home/ricardo/caminho/para/resultado/html/
Load template from Themes/default/support/document.html
Documentation site successfully generated
```

## 2. Definindo configurações

Para especificar um arquivo contendo as configurações, use o seguinte comando:

```bash
cd projeto/markdown
markhelp -c ../../config.json -o ../../caminho/para/resultado/html/
```

Para mais informações sobre como criar um arquivo contendo configurações, acesse [Personalizando e Configurando](configuracoes.md).

## Sumário

-   [Início](index.md)
-   [Instalando o MarkHelp](instalando.md)
-   [Implementando projetos PHP](utilizar-como-biblioteca.md)
-   [Utilizando no Terminal Linux](utilizar-no-terminal.md)
-   [Personalizando e Configurando](configuracoes.md)
-   [Quero ajudar o projeto](como-ajudar.md)
