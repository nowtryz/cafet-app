import { combineReducers } from 'redux'
import { createStore, applyMiddleware, compose } from 'redux'
import thunk from "redux-thunk";

import { isProd } from '../util'

import sample from './sample'
import user from './user'

export const reducer = combineReducers({
    sample,
    user
})

// eslint-disable-next-line no-underscore-dangle
const composeEnhancers = (isProd ? null : window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__) || compose

export const store = createStore(reducer, composeEnhancers(applyMiddleware(thunk)))

export default store