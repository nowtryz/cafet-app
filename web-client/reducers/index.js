import {
    combineReducers, createStore, applyMiddleware, compose,
} from 'redux'

import thunk from 'redux-thunk'

import { isProd } from '../config'

import user from './user'
import lang from './lang'
import server from './server'

export const reducer = combineReducers({
    user,
    lang,
    server,
})

// eslint-disable-next-line no-underscore-dangle
const composeEnhancers = (isProd ? null : window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__) || compose

const store = createStore(reducer, composeEnhancers(applyMiddleware(thunk)))

export default store
