const path = require('path');

module.exports = {
    entry: './js/src/index.js',
    output: {
        path: path.resolve(__dirname, 'js/dist'),
        filename: 'bundle.js',
    },
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
                use: ['style-loader', 'css-loader', 'sass-loader'],
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
