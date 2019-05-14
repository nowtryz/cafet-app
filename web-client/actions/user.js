import axios from 'axios'

import { API_URL } from '../config'

export const USER_IS_LOGGING = 'user_is_loging'
export const USER_LOGGED = 'user_logged'
export const USER_LOGGING_FAILED = 'user_logging_failed'

export const login = (user, password) => async (dispatch) => {
try {
    dispatch({
    type: USER_IS_LOGGING
    })

    const response = await axios.post( `${API_URL}/user/login`, {
        headers: {
            autorization: btoa(unescape(encodeURIComponent(`${user}:${password}`)))
        }
    })
    const { user } = response.data;

    dispatch({
    type: USER_LOGGED,
    payload: user
    });

} catch (err) {
    dispatch({
    type: USER_LOGGING_FAILED
    });
    if (redirectOnError) {
    window.location.href = `${API_URL}/login`;
    }
}
};


/**
 * Disconnect a user
 */
export const logout = () => () => {
window.location.href = `${API_URL}/logout`;
};