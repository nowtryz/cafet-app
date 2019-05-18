import axios from 'axios'

import {
    USER_IS_LOGGING,
    USER_LOGGED,
    USER_LOGGING_FAILED,
    USER_LOGOUT
} from 'constants'
import { API_URL } from '../config'
import store from '../reducers'

export const login = (username, password) => async dispatch => {
    try {
        dispatch({
            type: USER_IS_LOGGING,
            payload: `logging in as ${username}`
        })

        const response = await axios.post( `${API_URL}/user/login`, {
        }, {
            auth: {
                username,
                password
            }
        })

        const { message, session, user } = response.data

        dispatch({
            type: USER_LOGGED,
            payload: {
                user,
                session,
                message
            }
        })

    } catch (err) {
        dispatch({
            type: USER_LOGGING_FAILED,
            payload: err.response && err.response.data ? err.response.data.additional_message : null || 'logging failed' 
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
            payload: 'already logged out'
        })

        return
    }

    try {

        const response = await axios.post( `${API_URL}/user/logout`)

        const { message } = response.data

        dispatch({
            type: USER_LOGOUT,
            payload: message
        })

    } catch (err) {
        dispatch({
            type: USER_LOGOUT,
            payload: err.message
        })
    }
}