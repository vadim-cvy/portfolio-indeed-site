const path = require('path')
const glob = require('glob')

module.exports = () => ({
  /**
   * Entry points are detected by the following pattern: './assets/js/src/{entry-point-name}/index.ts
   */
  entry: () => {
    const entryPoints = {}

    const srcDir = path.resolve(__dirname, 'assets/js/src')
    const subDirs = glob.sync(`${srcDir}/*/index.ts`)

    subDirs.forEach(subDir => {
      subDir = path.resolve(__dirname, subDir )

      const entryPointName = path.basename(path.dirname(subDir))
      entryPoints[entryPointName] = subDir
    })

    return entryPoints
  },
  resolve: {
    extensions: ['.ts', '.js'],
  },
  module: {
    rules: [
      {
        test: /\.ts$/,
        use: 'ts-loader',
        exclude: /node_modules/,
      },
    ],
  },
})