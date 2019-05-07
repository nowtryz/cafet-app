

import { resolve } from 'path'
import webpack from 'webpack'

import ManifestPlugin  from 'webpack-manifest-plugin'

const isProd = process.env.NODE_ENV === 'production'
const WDS_PORT = 7000

export default {
  mode: process.env.NODE_ENV,
  devtool:  isProd ? false : 'source-map',
  entry: [
    'react-hot-loader/patch',
    './web-client',
  ],
  output: {
    //filename: isProd ? '[chunkhash].bundle.js' : 'bundle.js',
    filename: '[hash].[name].js',
    path: resolve(__dirname, 'server/dist'),
    publicPath: isProd ? '/dist/' : `http://localhost:${WDS_PORT}/dist/`,
  },
  module: {
    rules: [
        {
            test: /\.(js|jsx)$/,
            use: 'babel-loader',
            exclude: /node_modules/
        },
    ],
  },
  devServer: {
    contentBase: '/',
    publicPath: isProd ? '/dist/' : `http://localhost:${WDS_PORT}/dist/`,
    hot: true,
    port: WDS_PORT,
    headers: {
      'Access-Control-Allow-Origin': '*',
    },
    disableHostCheck: true
  },
  resolve: {
    extensions: ['.js', '.jsx'],
  },
  plugins: [
    new webpack.optimize.OccurrenceOrderPlugin(),
    new webpack.HotModuleReplacementPlugin(),
    new webpack.NamedModulesPlugin(),
    new webpack.NoEmitOnErrorsPlugin(),
    new ManifestPlugin({
      writeToFileEmit: true,
    }),
  ],
}