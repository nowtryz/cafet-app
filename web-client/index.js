import '@babel/polyfill'
import 'react-hot-loader'

import ReactDOM from 'react-dom'
import axios from 'axios'
import { createBrowserHistory } from 'history'

import App from './containers/app'
import wrapper from './containers/wrapper'
import store from './reducers'
import { responseInterceptor, errorInterceptor } from './interceptors'

import {
    loadServerConfig as loadServerConfigAction,
    grabUserInfo as grabUserInfoAction,
} from './actions'

axios.interceptors.response.use(responseInterceptor, errorInterceptor)
axios.defaults.headers.common['Skip-Headers'] = '"WWW-Authenticate"'

const rootEl = document.querySelector('.app')
const history = createBrowserHistory()

store.dispatch(loadServerConfigAction())
    .then(() => store.dispatch(grabUserInfoAction()))
    .then(() => {
        ReactDOM.render(wrapper(App, store, history), rootEl)
    })

if (module.hot) {
    module.hot.accept('./containers/app', () => {
    // eslint-disable-next-line global-require
        const NextApp = require('./containers/app').default
        ReactDOM.render(wrapper(NextApp, store, history), rootEl)
    })
}
