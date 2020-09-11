#!/bin/bash

GREEN='\033[0;32m';
BLUE='\e[34m';
NC='\033[0m';


echo -e "------------------------------------------------------------------";
echo -e "Gerando um pacote do MarkHelp";
echo -e "------------------------------------------------------------------";

echo -e "${BLUE}→ Verificando e instalando as dependencias${NC}";
composer install;


PATH_ROOT=$(cd "$(dirname "$0")" && pwd);
PATH_DIST="$PATH_ROOT/dist";
PATH_PACKAGE="$PATH_DIST/markhelp";
PATH_PACKAGE_SHARE="$PATH_PACKAGE/usr/share/markhelp";
PATH_PACKAGE_BIN="$PATH_PACKAGE/usr/bin";

echo -e "${BLUE}→ Verificando a versão da biblioteca${NC}";
# se a tag possuir o "v" no inicio
VERSION=$(git describe --tags $(git rev-list --tags --max-count=1));
if [ "${VERSION:0:1}" == "v"  ]; then
    # extrai o "v"
    VERSION=${VERSION:1:5}; 
fi

# atualiza o arquivo de versão
echo $VERSION > version.app;

# cria o diretório que possuirá o conteudo do pacote
mkdir -p $PATH_DIST;
cd $PATH_DIST;

echo -e "${BLUE}→ Gerando um pacote Debian${NC}";

# gera a estrutura do pacote
mkdir -p $PATH_PACKAGE/DEBIAN;
mkdir -p $PATH_PACKAGE_BIN;
mkdir -p $PATH_PACKAGE_SHARE;
mkdir -p $PATH_PACKAGE_SHARE/src;
mkdir -p $PATH_PACKAGE_SHARE/vendor;

# cria o arquivo de controle
touch $PATH_PACKAGE/DEBIAN/control;
cat > $PATH_PACKAGE/DEBIAN/control <<EOF 
Package: markhelp
Priority: optional
Version: $VERSION
Architecture: all
Maintainer: Ricardo Pereira Dias <contato@ricardopedias.com.br>
Depends: php-cli
Description: Ferramenta para gerar sites HTML a partir de arquivos Markdown
EOF

# copia o conteudo
sudo cp $PATH_ROOT/markhelp $PATH_PACKAGE_SHARE/;
sudo cp $PATH_ROOT/version.app $PATH_PACKAGE_SHARE/;
sudo chmod a+x $PATH_PACKAGE_SHARE/markhelp;
sudo cp -rf $PATH_ROOT/src/ $PATH_PACKAGE_SHARE/;
sudo cp -rf $PATH_ROOT/vendor/ $PATH_PACKAGE_SHARE/;

# cria o arquivo de atalho global
cat > $PATH_PACKAGE_BIN/markhelp <<EOF 
#!/bin/bash
/usr/share/markhelp/markhelp \$@
EOF

sudo chmod a+x $PATH_PACKAGE_BIN/markhelp;

# gera o pacote deb
dpkg-deb -b "$PATH_PACKAGE/" $PATH_ROOT;

echo -e "${BLUE}→ Limpando dados de compilação${NC}";
sudo rm -Rf $PATH_DIST;

echo -e "${GREEN}→ Pacote gerado com sucesso${NC}";
