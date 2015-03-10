var webpack = require('webpack');
var yargs = require('yargs');
var path = require('path');

var argv = yargs
  .boolean('p').alias('p', 'optimize-minimize')
  .boolean('h').alias('h', 'hot')
  .argv;

module.exports = {
  entry: (function() {
    var entry = [];

    if (argv.h) {
      entry.push('webpack-dev-server/client?/');
      entry.push('webpack/hot/dev-server');
    }

    entry.push('./src/frontend/admin.js');

    return entry;
  })(),

  output: {
    path: path.join(__dirname, 'web', 'admin'),
    filename: 'admin.js',
    publicPath: '/admin/'
  },

  module: {
    loaders: [
      {test: /\.js$/, exclude: /\/node_modules\//, loaders: [
      ]},

      {test: /\.jsx$/, loaders: (function() {
        var loaders = [];

        if (argv.h) {
          loaders.push('react-hot');
        }

        loaders.push('jsx');

        return loaders;
      })()},

      {test: /\.less$/, loaders: [
        'style',
        'css',
        'autoprefixer',
        'less'
      ]},

      {test: /\.css$/, loaders: [
        'style',
        'css',
        'autoprefixer'
      ]},

      {test: /\.yml$/, loaders: [
        'yml'
      ]},

      {test: /\.json$/, loaders: [
        'json'
      ]},

      {test: /\.(png|jpg|gif)$/, loaders: [
        'url?limit=50000'
      ]},

      {test: /\.(ttf|eot|woff|svg)$/, loaders: [
        'file'
      ]}
    ]
  },

  resolve: {
    extensions: ['', '.js', '.jsx']
  },

  debug: !argv.p,

  plugins: (function() {
    var plugins = [];

    plugins.push(
      new webpack.DefinePlugin({
        'process.env.NODE_ENV': JSON.stringify(argv.p ? 'production' : 'development')
      })
    );

    if (argv.p) {
      plugins.push(new webpack.optimize.UglifyJsPlugin());
    }

    if (argv.h) {
      plugins.push(new webpack.HotModuleReplacementPlugin());
    }

    return plugins;
  })()
};