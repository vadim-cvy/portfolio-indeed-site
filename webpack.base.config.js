const path = require('path')
const glob = require('glob')

module.exports = () => ({
  entry: () => {
    const entryPoints = {}

    const srcDir = path.resolve(__dirname, 'assets/src/js')
    const subDirs = glob.sync(`${srcDir}/*/index.ts`)

    subDirs.forEach(subDir => {
      subDir = path.resolve(__dirname, subDir )

      const entryPointName = path.basename(path.dirname(subDir))
      entryPoints[entryPointName] = subDir
    })

    return entryPoints
  },
  output: {
    path: path.resolve(__dirname, 'assets/dist/js'),
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
  externals: {
    vue: 'Vue',
    swal: 'Swal',
  },
})