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

    entry.push('./src/frontend/src/index.coffee');

    return entry;
  })(),

  output: {
    path: path.join(__dirname, 'web', 'client'),
    filename: 'client.js',
    publicPath: '/client/'
  },

  module: {
    loaders: [
      {test: /\.js$/, exclude: /\/node_modules\//, loaders: [
      ]},

      {test: /\.coffee$/, exclude: /\/node_modules\//, loaders: [
        'jsx',
        'coffee'
      ]},

      {test: /\.styl$/, loaders: [
        'style',
        'css',
        'stylus'
      ]},

      // {test: /\.(png|jpg|gif)$/, loaders: [
      //   'url?limit=50000'
      // ]},
      //
      // {test: /\.(ttf|eot|woff|svg)$/, loaders: [
      //   'file'
      // ]}
    ]
  },

  resolve: {
    extensions: ['', '.js', '.coffee']
  },

  externals: {
    'jquery': 'var jQuery'
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
