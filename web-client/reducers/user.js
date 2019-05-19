import {
    USER_IS_LOGGING,
    USER_LOGGED,
    USER_LOGGING_FAILED,
    USER_LOGOUT
} from 'constants'

const initialState = {
    user: null,
    isLogging: false,
    session: null,
    message: 'not logged'
}

const userReducer = (state = initialState, { type, payload = null }) => {
    switch (type) {
    case USER_IS_LOGGING: return {
        ...state,
        isLogging: true,
        message: payload
    }

    case USER_LOGGING_FAILED: return {
        ...state,
        isLogging: false,
        message: payload
    }

    case USER_LOGGED: return {
        ...state,
        isLogging: false,
        user: payload.user,
        session: payload.session,
        message: payload.message
    }

    case USER_LOGOUT: return {
        ...state,
        user: null,
        message: payload
    }

    default:
        return state
    }
}

export default userReducer