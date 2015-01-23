var format = require('util').format;
var path = require('path');
var WebpackDevServer = require('webpack-dev-server');
var webpack = require('webpack');
var config = require('./webpack.config');

var argv = require('yargs')
  .boolean('h').alias('h', 'hot')
  .argv;

var contentBase;
if (process.env.SOURCE_IP && process.env.SOURCE_PORT) {
  contentBase = {
    target: format(
      'http://%s:%d/',
      process.env.SOURCE_IP,
      process.env.SOURCE_PORT
    )
  };
} else {
  contentBase = path.join(__dirname, 'public');
}

var port = process.env.PORT || 3000;
var host = process.env.IP || '0.0.0.0';

var server = new WebpackDevServer(webpack(config), {
  publicPath: config.output.publicPath,
  hot: argv.h,
  contentBase: contentBase
});

server.listen(port, host, function(err) {
  if (err) {
    console.log(err);
  }
});
