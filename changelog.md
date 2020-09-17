# Changelog

Este é o registro contendo as alterações mais relevantes efetuadas no projeto
seguindo o padrão que pode ser encontrado em [Keep a Changelog](https://keepachangelog.com/en/1.0.0).

Para obter o diff para uma versão específica, siga para o final deste documento 
e acesse a URL da versão desejada. Por exemplo, v4.0.0 ... v4.0.1.
As versões seguem as regras do [Semantic Versioning](https://semver.org/lang/pt-BR).

## \[Unreleased]

Nada implementado ainda.

## \[1.0.0] - 2020-09-17

### Added

-   Melhorias na documentação
-   Adicionada geração automática do menu lateral com base na estrutura do projeto
-   Adicionada biblioteca twig/twig para renderização de templates
-   Adicionados analisadores para qualidade de código
-   Adicionado componente choices.js como seletor de versões

### Changed

-   Melhoria na arquitetura do projeto
-   Adicionados templates twig ao tema padrão

### Fixed 

-   Remoção de barras duplas nas urls geradas pelo processo de interpretação

## \[0.7.0] - 2020-04-01

### Added

-   Melhorias na documentação
-   Adicionado o badge do Travis
-   Adicionada possibilidade de renderização com base em repositórios GIT
-   Adicionada renderização de várias versões com base em branchs

### Fixed 

-   Pequenas irregularidades de layout

## \[0.6.1] - 2020-03-18

### Fixed 

-   Assets não eram substituídos corretamente no template do documento
-   Geração de diretórios inválidos com base nas imagens do projeto
-   Correção de bug que ignorava a conversão de urls com hash de fragmento

## \[0.6.0] - 2020-03-17

### Added

-   Suporte a exibição da versão real da biblioteca na linha de comando
-   Suporte a utilização de imagens dentro da documentação
-   Melhorias na legibilidade do código fonte

### Fixed 

-   Script de geração de pacotes Debian para contemplar novo arquivo version.app
-   Configuração errada quando um arquivo de suporte inexistente era especificado

## \[0.5.1] - 2020-03-16

### Added

-   Melhorias na documentação

### Fixed 

-   Validações de diretórios diretamente na setagem de configurações

## \[0.5.0] - 2020-03-13

### Added

-   Criada a aplicação MarkHelp
-   Adicionado carregamento de configurações via arquivo json
-   Adicionado script para execução via terminal

### Fixed

-   Bug no carregamento de arquivos de configuração fazia 
    o Filesystem gerar diretórios com as palavras chave null, true ou false

### Removed

-   Remoção de diretórios depreciados pela refatoração

## \[0.4.0] - 2020-03-06

### Added

-   Adicionados scripts para execução de testes via composer.

### Changed

-   Melhoria na arquitetura do projeto.
-   Melhoria no tema padrão

## \[0.3.0] - 2020-02-08

### Changed

-   Diversas refatorações e otimizações no código fonte.

## \[0.2.1] - 2020-02-07

### Added

-   Adicionado prefixos corretos nos links das páginas html.

### Fixed

-   Diversos code smells identificados pelo Codacy

## \[0.2.0] - 2020-02-06

### Added

-   Criação do tema padrão para as documentações.
-   Adicionado correção de links de arquivos .md para .html.

## \[0.1.0] - 2020-02-05

### Added

-   Criação da estrutura básica do projeto.

## Releases

-   Unreleased <https://github.com/ricardopedias/markhelp/compare/v0.7.0...HEAD>
-   0.7.0 <https://github.com/ricardopedias/markhelp/compare/v0.6.1...v0.7.0>
-   0.6.1 <https://github.com/ricardopedias/markhelp/compare/v0.6.0...v0.6.1>
-   0.6.0 <https://github.com/ricardopedias/markhelp/compare/v0.5.1...v0.6.0>
-   0.5.1 <https://github.com/ricardopedias/markhelp/compare/v0.5.0...v0.5.1>
-   0.5.0 <https://github.com/ricardopedias/markhelp/compare/v0.4.0...v0.5.0>
-   0.4.0 <https://github.com/ricardopedias/markhelp/compare/v0.3.0...v0.4.0>
-   0.3.0 <https://github.com/ricardopedias/markhelp/compare/v0.2.1...v0.3.0>
-   0.2.1 <https://github.com/ricardopedias/markhelp/compare/v0.2.0...v0.2.1>
-   0.2.0 <https://github.com/ricardopedias/markhelp/compare/v0.1.0...v0.2.0>
-   0.1.0 <https://github.com/ricardopedias/markhelp/releases/tag/v0.1.0>
