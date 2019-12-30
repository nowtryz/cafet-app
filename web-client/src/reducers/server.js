import { SERVER_INFO_LOADED } from 'constants'

const initialState = {
    debug: false,
    production: true,
    organisation: 'Cafet',
    lang: 'fr_FR',
    currency: '€',
    session_name: '_cafetapp_session',
}

const serverReducer = (state = initialState, { type, payload = null }) => {
    switch (type) {
    case SERVER_INFO_LOADED:
        return {
            ...state,
            ...payload,
        }

    default: return state
    }
}

export default serverReducer