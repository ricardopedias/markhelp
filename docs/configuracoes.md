# Utilizando o MarkHelp no terminal

O MarkHelp é capaz de transformar qualquer projeto contendo arquivos markdown em HTML, formatando-os de uma forma padrão. Mas é possível personalizar várias coisas, de forma que a geração dos documentos se adapte às mais diversas necessidades.

## 1. Personalizando o menu lateral

Para personalizar o menu lateral basta adicionar, na raiz do projeto markdown, uma arquivo chamado `menu.json`. Este arquivo deve possuir a seguinte formatação:

```json
{
    "Item Simples 1" : "item-um.md",
    "Item Simples 2" : "item-dois.md",

    "Titulo 1" : {
        "Item Agrupado 1" : "Diretório Um/item-três.md",
        "Item Agrupado 2" : "Diretório Um/item-quatro.md"
    },

    "Título 2": {

        "Item Agrupado 3" : {
            "Subitem 1" : "Diretório Dois/item-cinco.md",
            "Subitem 2" : "item-seis.md",
            "Subitem 3" : "item-sete.md"
        },

        "Item Agrupado 4" : {
            "Subitem 4" : "Diretório Três/item-oito.md",
            "Subitem 5" : "Diretório Três/item-nove.md"
        }
    }
}
```

### Entendendo a estrutura

Analisando o objeto Json, vejamos o que significa cada item na estrutura:

1. **Nível 1**: se o valor for uma url, esses itens serão renderizados diretamente no primeiro nível do menu;
2. **Nível 1 (objeto como valor)**: se o valor for um outro objeto, os itens dele serão considerados *grupos de itens*. Isso significa que eles serão agrupados em um bloco delimitado por um título;
3. **Nível 2**: se o valor for uma url, esses itens serão renderizados diretamente no menu, dentro da delimitação do título;
4. **Nível 2 (objeto como valor)**: se o valor for um outro objeto, os itens dele serão considerados um *submenu*. Isso significa que eles estarão ocultos, devendo ser clicado no item para expandir os demais *sub-items*;

Abaixo, um exemplo desta estrutura após a renderização.

![Menu Lateral](images/menu-lateral.png)


## 2. Configurações avançadas


### Mudando o tema

Esta parte da documentação ainda está sendo elaborada.

### Mudando o logotipo


Esta parte da documentação ainda está sendo elaborada.

### Outras configurações


Esta parte da documentação ainda está sendo elaborada.



## Sumário

-   [Início](index.md)
-   [Instalando](instalando.md)
-   [Utilizando em projetos PHP](utilizar-como-biblioteca.md)
-   [Utilizando no Terminal Linux](utilizar-no-terminal.md)
-   [Personalizando e Configurando](configuracoes.md)
-   [Quero ajudar o projeto](como-ajudar.md)
