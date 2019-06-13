import axios from 'axios'

import {
    USER_IS_LOGGING,
    USER_LOGGED,
    USER_LOGGING_FAILED,
    USER_LOGOUT
} from 'constants'
import { API_URL } from '../config'
import store from '../reducers'

export const grabUserInfo = () => async dispatch => {
    const session_name = store.getState().server.session_name
    const session = document.cookie.replace(new RegExp(`(?:(?:^|.*;\\s*)${session_name}\\s*=\\s*([^;]*).*$)|^.*$`, 'g'), '$1') || null

    if (!session) return

    try {
        dispatch({
            type: USER_IS_LOGGING,
            message: 'checking session'
        })

        const response = await axios.get(`${API_URL}/user/current`)

        dispatch({
            type: USER_LOGGED,
            user: response.data,
            session,
            message: 'logged from session'
        })
    } catch (err) {
        dispatch({
            type: USER_LOGGING_FAILED,
            message: (err.response && err.response.data ? err.response.data.additional_message : null) || 'logging from session failed'
        })
    }
}

export const login = (username, password) => async dispatch => {
    try {
        dispatch({
            type: USER_IS_LOGGING,
            message: `logging in as ${username}`
        })

        const response = await axios.post(`${API_URL}/user/login`, {
        }, {
            auth: {
                username,
                password
            }
        })

        const { message, session, user } = response.data

        dispatch({
            type: USER_LOGGED,
            user,
            session,
            message
        })

    } catch (err) {
        dispatch({
            type: USER_LOGGING_FAILED,
            message: (err.response && err.response.data ? err.response.data.additional_message : null) || 'logging failed'
        })
    }
}


/**
 * Disconnect a user
 */
export const logout = () => async dispatch => {
    if (!store.getState().user.user) {
        dispatch({
            type: USER_LOGOUT,
            message: 'already logged out'
        })

        return
    }

    try {

        const response = await axios.post( `${API_URL}/user/logout`)

        const { message } = response.data

        dispatch({
            type: USER_LOGOUT,
            message: message
        })

    } catch (err) {
        dispatch({
            type: USER_LOGOUT,
            message: err.message
        })
    }
}

export const clearUser = () => ({
    type: USER_LOGOUT,
    message: 'disconnected from the server'
})