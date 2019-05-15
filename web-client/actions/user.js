import axios from 'axios'

import { API_URL } from '../config'

export const USER_IS_LOGGING = 'user_is_loging'
export const USER_LOGGED = 'user_logged'
export const USER_LOGGING_FAILED = 'user_logging_failed'
export const USER_LOGOUT = 'user_logout'

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
            },
            headers: {
                'Skip-Headers': '"WWW-Authenticate"'
            }
        })

        const { message, session, user } = response.data;

        dispatch({
        type: USER_LOGGED,
        payload: {
            user,
            session,
            message
        }
        });

    } catch (err) {
        console.log(err.response)
        dispatch({
            type: USER_LOGGING_FAILED,
            payload: err.response && err.response.data ? err.response.data.additional_message : null || 'logging failed' 
        });
    }
};


/**
 * Disconnect a user
 */
export const logout = () => async dispatch => {
    try {

        const response = await axios.post( `${API_URL}/user/logout`)

        const { message } = response.data;

        dispatch({
        type: USER_LOGOUT,
        payload: message
        });

    } catch (err) {
        console.error(err)
    }
};