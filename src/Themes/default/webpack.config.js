
const path = require('path'); //um modulo nativo do node!
const miniCssExtractPlugin = require('mini-css-extract-plugin'); // Ele cria um arquivo CSS por arquivo JS que contém CSS.
const isProduction = process.env.NODE_ENV === 'production';

const MiniCssExtractPlugin    = require('mini-css-extract-plugin');

module.exports = {
    // ponto de entrada. especifico qual será o 1º modulo a ser carregado
    entry: './resources/js/theme.js', 
    // cria o arquivo scripts.js na pasta 'assets'
    output: {
        filename: 'scripts.js', 
        // __dirname = src/Themes/default
        // path.resolve() cria o caminho completo até a pasta 'assets'
        path: path.resolve(__dirname, 'assets'),
        // qdo servidor rodando, o scripts.js é gerado em memoria pelo webpackdevserver. 
        // este parametro define que o scripts.js estará dentro da pasta 'assets'
        publicPath: 'assets'
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'styles.css'
        })
      ],
    // o module permite ter varias regras de execução
    module: {
        // cada regra pode usar um módulo específico quando aplicada.
        rules: [
            {
                // a condição na qual o 'babel-loader' será aplicado.
                test: /\.js$/, 
                // durante o processo, excluímos a pasta node_modules, 
                // pois não faz sentido processar os arquivos dela
                exclude: /node_modules/,
                // babel-loader: A ponte de ligação entre o Webpack e o babel-core
                use: 'babel-loader'
            },
            {
                test: /\.css$/,
                exclude: /node_modules/,
                use: [
                    miniCssExtractPlugin.loader, 
                    'css-loader'
                ]
            },
            {
                test: /\.scss$/,
                exclude: /node_modules/,
                use: [
                    miniCssExtractPlugin.loader,
                    'css-loader',
                    'sass-loader'
                ]
            },
            { 
                test: /\.(woff|woff2)(\?v=\d+\.\d+\.\d+)?$/, 
                loader: 'url-loader?limit=10000&mimetype=application/font-woff' 
            },
            { 
                test: /\.ttf(\?v=\d+\.\d+\.\d+)?$/, 
                loader: 'url-loader?limit=10000&mimetype=application/octet-stream'
            },
            { 
                test: /\.eot(\?v=\d+\.\d+\.\d+)?$/, 
                loader: 'file-loader' 
            },
            { 
                test: /\.svg(\?v=\d+\.\d+\.\d+)?$/, 
                loader: 'url-loader?limit=10000&mimetype=image/svg+xml' 
            }  
        ]
    },
    mode: isProduction ? 'production' : 'development',
};
