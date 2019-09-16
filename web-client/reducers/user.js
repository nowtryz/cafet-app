import {
    USER_IS_LOGGING,
    USER_LOGGED,
    USER_LOGGING_FAILED,
    USER_LOGOUT,
} from 'constants'

const initialState = {
    user: null,
    isLogging: false,
    session: null,
    message: 'not logged',
}

const userReducer = (state = initialState, action) => {
    switch (action.type) {
    case USER_IS_LOGGING: return {
        ...state,
        isLogging: true,
        message: action.message,
    }

    case USER_LOGGING_FAILED: return {
        ...state,
        isLogging: false,
        message: action.message,
    }

    case USER_LOGGED: return {
        ...state,
        isLogging: false,
        user: action.user,
        session: action.session,
        message: action.message,
    }

    case USER_LOGOUT: return {
        ...state,
        user: null,
        session: null,
        message: action.message,
    }

    default:
        return state
    }
}

export default userReducer
