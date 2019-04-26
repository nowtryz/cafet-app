

import { resolve } from 'path'

import LiveReloadPlugin from 'webpack-livereload-plugin'

export default {
  mode: process.env.NODE_ENV,
  entry: [
    './js',
  ],
  output: {
    filename: 'bundle.js',
    path: resolve(__dirname, 'app/dist'),
    publicPath: '/js/',
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