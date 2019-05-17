import '@babel/polyfill'

import React from 'react'
import ReactDOM from 'react-dom'
import { AppContainer } from 'react-hot-loader'
import { Provider } from 'react-redux'
import axios from 'axios'

import App from './app'
import store from './reducers'
import { responseInterceptor, errorInterceptor } from './interceptors'


axios.interceptors.response.use(responseInterceptor, errorInterceptor)
axios.defaults.headers.common['Skip-Headers'] = '"WWW-Authenticate"'

const rootEl = document.querySelector('.app')

const wrapApp = (AppComponent, reduxStore) =>
  <Provider store={reduxStore}>
    <AppContainer>
      <AppComponent />
    </AppContainer>
  </Provider>

ReactDOM.render(wrapApp(App, store), rootEl)

if (module.hot) {
  module.hot.accept('./app', () => {
    // eslint-disable-next-line global-require
    const NextApp = require('./app').default
    ReactDOM.render(wrapApp(NextApp, store), rootEl)
  })
}
