const path = require('path')
const glob = require('glob')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = () => ({
  entry: () => {
    const entryPoints = {}

    const srcDir = path.resolve(__dirname, 'assets/src')
    const subDirs = glob.sync(`${srcDir}/*/index.ts`)

    subDirs.forEach(subDir => {
      subDir = path.resolve(__dirname, subDir )

      const entryPointName = path.basename(path.dirname(subDir))
      entryPoints[entryPointName] = subDir
    })

    return entryPoints
  },
  output: {
    path: path.resolve(__dirname, 'assets/dist'),
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
      {
        test: /\.s[ac]ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'sass-loader'
        ],
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name]/index.css',
    }),
  ],
})