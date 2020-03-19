# Utilizando o MarkHelp no terminal

O MarkHelp foi escrito para funcionar em sistemas Linux. Por isso, o projeto 
acompanha um script para geração de pacotes Debian. 
Veja mais sobre isso [clicando aqui](instalando.md#em-ambiente-linux).

Após a correta instalação, o terminal possíbilita uma utilização global do MarkHelp,
sem a necessidade de programar. Isso é muito útil para quando a necessidade é apenas
gerar uma documentação HTML e disponibilizá-la manualmente em algum canal na internet.

> Obs: para automatizar o processo de criação de documentações, veja [Utilizar como Biblioteca](utilizar-como-biblioteca.md).

## Uso básico

Dentro do terminal, basta digitar o comando markhelp --help para exibir a lista de ajuda.

```bash
markhelp --help
```

O uso mais simples é:

```bash
markhelp -i ./caminho/ate/meu/projeto/markdown/ -o ./caminho/para/minhas/paginas/html/
```

O resultado será parecido com o mostrado abaixo:

```bash
---------------------------------------------------
MarkHelp 0.6.1
---------------------------------------------------
Reading from: /home/ricardo/caminho/ate/meu/projeto/markdown/
Saving in /home/ricardo/caminho/para/minhas/paginas/html/
Load template from Themes/default/support/document.html
Documentation site successfully generated
```

## Sumário

-   [Início](index.md)
-   [Instalando](instalando.md)
-   [Utilizando em projetos PHP](utilizar-como-biblioteca.md)
-   [Utilizando no Terminal Linux](utilizar-no-terminal.md)
-   [Personalizando e Configurando](configuracoes.md)
-   [Quero ajudar o projeto](como-ajudar.md)
