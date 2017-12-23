const path = require('path');
const HTMLGen = require('html-webpack-plugin');
const Cleaner = require('clean-webpack-plugin');
const webpack = require('webpack');

const assetsPath = path.resolve(__dirname, 'assets');
const srcPath = path.resolve(assetsPath, 'src');
const buildPath = path.resolve(assetsPath, 'dest');

module.exports = {
    entry: {
        app: path.resolve(srcPath, 'app.js'),
    },
    output: {
        filename: '[name].[hash].js',
        path: buildPath,
    },
    externals: {
        jquery: '$'
    },
    module: {
        loaders: [
            {
                test: /\.js$/,
                loader: "babel-loader",
                include: srcPath,
                options: {
                    presets: ['babel-preset-env'],
                    plugins: ['babel-plugin-transform-runtime'],
                },
            },
        ]
    },
    plugins: [
        new Cleaner(['assets/dest/*.js'], {
            root: __dirname,
        }),
        new webpack.optimize.UglifyJsPlugin(),
        new HTMLGen({
            filename: 'index.html',
        })
    ]
};
