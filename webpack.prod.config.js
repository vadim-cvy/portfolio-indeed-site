const path = require('path')

const config = require('./webpack.base.config')()

config.mode = 'production'

config.output.filename = '[name]/index.prod.js'

module.exports = config