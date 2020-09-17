# Personalizando e configurando

O MarkHelp é capaz de transformar qualquer projeto contendo arquivos markdown em HTML, formatando-os com um design padrão. 

Mas é possível personalizar várias coisas, de forma que a geração dos documentos se adapte às mais diversas necessidades.

## 1. Configurações avançadas

O MarkHelp possui várias configurações pontuais, que mudam seu comportamento no momento da renderização de páginas HTML.

Essas configurações podem ser especificadas criando um arquivo `config.json` dentro do projeto markdown, ou especificando sua localização diretamente na [implementação](utilizar-como-biblioteca.md) ou na [linha de comando](utilizar-no-terminal.md) do MarkHelp. 

Abaixo, seguem todas elas:

* **path_theme**: define o caminho completo até o tema desejado. Os temas padrões se encontram no diretório [src/Themes](https://github.com/ricardopedias/markhelp/tree/master/src/Themes/) do código fonte do MarkHelp. Você pode copiar este diretório, fazer as alterações desejadas no tema e passar seu caminho completo neste parâmetro de configuração;
* **clone_url**: define a url para acessar o repositório do projeto;
* **clone_directory**: define o diretório, dentro do repositório do git, de onde a documentação em markdown deverá ser extraída;
* **clone_tags**: define as `tags` a serem usadas como versões do documento. Para especificar mais de uma `tag`, basta adicioná-las separadas por vírgula.
* **copy_name**: o nome da pessoa/instituição detentora dos direitos da documentação renderizada;
* **copy_url**: a url para o site da pessoa/instituição detentora dos direitos;
* **project_name**: define o nome do projeto ao qual a documentação renderizada se refere;
* **project_slogan**: define uma pequena frase, que aparecerá no cabeçalho, como slogan do projeto;
* **project_fork**: define se a bandeira de "Faça um fork" deverá, ou não aparecer [true ou false]; 
* **project_description**: define um texto descritivo para a documentação ser indexada pelos mecanismos de busca. Especifique no máximo 255 caracteres para uma boa descrição;
* **project_logo_status**: define se o logotipo aparecerá ou não no cabeçalho das páginas HTML. Por padrão, o valor setado é `true`;
* **project_logo**: define o caminho até o logotipo da documentação. Por padrão, o logotipo do MarkHelp será utilizado;

> **Observação**: usando o MarkHelp como biblioteca em projetos PHP, é possível setar configurações pontuais, sem a necessidade de usar um arquivo `config.json`. Para mais informações, confira [Implementando projetos PHP](utilizar-como-biblioteca.md).

## 2. Personalizando o menu lateral

O menu lateral é gerado automaticamente com base na estrutura de diretórios da coleção de arquivos markdown existente no projeto.

Os títulos dos arquivos são utilizados para nomear os itens de menu, como pode ser conferido no exemplo abaixo:

![Menu Lateral](images/menu-lateral.png)

### Entendendo mais a fundo

Na imagem acima, pode-se observar como o MarkHelp "entende" a estrutura de arquivos do projeto markdown. A seguinte regra é usada para desenhar o menu lateral:

1. **Itens (ex: Introdução)**: seus nomes são baseados nos títulos dos arquivos markdown;
2. **Títulos (ex: O BÁSICO)**: são baseados nos nomes dos diretórios que agrupam os subitens de segundo nível;
3. **Submenus (ex: Configurações)**: são baseados nos nomes dos diretórios que agrupam os subitens de terceiro nível;

> **Importante:** não coloque espaços nos nomes de arquivos e diretórios. Ao invés disso, separe as palavras compostas usando "-" ou "_".

### Ordenação dos Itens

Por padrão, os itens são ordenados afabeticamente:

```
arquitetura.md
design-patterns.md
```

Mas é possível obter um controle maior sobre isso, colocando um prefixo numérico em cada elemento do projeto. Isso forçará uma ordenação personalizada:

```
01-design-patterns.md
02-arquitetura.md
```

Na imagem anterior de exemplo, você pode observar que os arquivos e diretórios estão nomeados com um prefixo numérico, adequando-os corretamente no menu lateral.

## Sumário

-   [Início](index.md)
-   [Usando como Biblioteca](utilizar-como-biblioteca.md)
-   [Usando no Terminal](utilizar-no-terminal.md)
-   [Personalizando e Configurando](configuracoes.md)
-   [Quero ajudar o projeto](como-ajudar.md)
