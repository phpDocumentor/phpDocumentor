const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
    mode: 'production',
    entry: './js/src/index.js',
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'bundle.js',
    },
    plugins: [
        new MiniCssExtractPlugin()
    ],
    module: {
        rules: [
            {
                test: require.resolve("fuse.js/dist/fuse.common.js"),
                loader: "expose-loader",
                options: {
                    exposes: {"globalName": "Fuse"},
                },
            },
            {
                test: require.resolve("css-vars-ponyfill/dist/css-vars-ponyfill.js"),
                loader: "expose-loader",
                options: {
                    exposes: {"globalName": "cssVars"},
                },
            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {
                            publicPath: 'dist'
                        }
                    },
                    'css-loader',
                    'postcss-loader',
                    'sass-loader'
                ],
            },
            {
                test: /\.(svg|eot|woff|woff2|ttf)$/,
                type: 'asset/resource',
                generator: {
                    publicPath: 'dist/',
                    filename: 'fonts/[hash][ext][query]'
                }
            },
        ]
    }
};
