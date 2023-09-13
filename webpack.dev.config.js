const path = require('path')

const config = require('./webpack.base.config')()

config.watch = true

config.mode = 'development'

config.output = {
  filename: '[name]/index.dev.js',
  path: path.resolve(__dirname, 'assets/js/dist'),
}

module.exports = config