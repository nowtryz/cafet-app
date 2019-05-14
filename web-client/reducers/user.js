import { USER_IS_LOGGING, USER_LOGGED, USER_LOGGING_FAILED } from '../actions'

const initialState = {
  user: null,
  isLogging: false
}

const sampleReducer = (state = initialState, { type, payload = null }) => {
  switch (type) {
    case USER_IS_LOGGING: return {
        ...state,
        isLogging: true
    }

    case USER_LOGGING_FAILED: return {
        ...state,
        isLogging: false
    }

    case USER_LOGGED: return {
        ...state,
        isLogging: false,
        user, payload
    }

    default:
        return state
  }
}

export default sampleReducer