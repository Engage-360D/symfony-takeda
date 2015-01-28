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

    entry.push(path.join(__dirname, 'src', 'frontend', 'index'));

    return entry;
  })(),

  output: {
    path: path.join(__dirname, 'web', 'assets'),
    filename: 'app.js',
    publicPath: '/assets/',
    library: 'takeda'
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

      {test: /\.(png|jpg|gif)$/, loaders: (function() {
        var loaders = [];

        loaders.push('url?limit=50000');

        if (argv.p) {
          loaders.push('image');
        }

        return loaders;
      })()},

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
