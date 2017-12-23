const path = require('path');
const assetsPath = path.resolve(__dirname, 'assets');
const buildPath = path.resolve(__dirname, 'Web', 'js');

module.exports = {
    entry: {
        app: path.resolve(assetsPath, 'src', 'app.js')
    },
    output: {
        filename: '[name].js',
        path: buildPath
    },
    externals: {
        jquery: '$'
    }
};