import '@babel/polyfill'


import ReactDOM from 'react-dom'
import axios from 'axios'

import App from './containers/app'
import wrapper from './containers/wrapper'
import store from './reducers'
import { responseInterceptor, errorInterceptor } from './interceptors'

import {
    loadServerConfig as loadServerConfigAction,
    grabUserInfo as grabUserInfoAction
} from './actions'

axios.interceptors.response.use(responseInterceptor, errorInterceptor)
axios.defaults.headers.common['Skip-Headers'] = '"WWW-Authenticate"'

const rootEl = document.querySelector('.app')

store.dispatch(loadServerConfigAction())
    .then(() => store.dispatch(grabUserInfoAction()))
    .then(() => {
        ReactDOM.render(wrapper(App, store), rootEl)
    })

if (module.hot) {
    module.hot.accept('./containers/app', () => {
    // eslint-disable-next-line global-require
        const NextApp = require('./containers/app').default
        ReactDOM.render(wrapper(NextApp, store), rootEl)
    })
}
