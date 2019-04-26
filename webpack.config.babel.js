

import { resolve } from 'path'

import LiveReloadPlugin from 'webpack-livereload-plugin'

export default {
  mode: process.env.NODE_ENV,
  entry: [
    './web-client',
  ],
  output: {
    filename: 'bundle.js',
    path: resolve(__dirname, 'server/dist'),
    publicPath: '/dist/',
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
  resolve: {
    extensions: ['.js', '.jsx'],
  },
  plugins: [
    new LiveReloadPlugin({
        port: 8080
    })
  ]
}