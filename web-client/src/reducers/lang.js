import { LOCALE_CHANGED } from '../constants'
import fr from '../lang/fr_FR.json'
import en from '../lang/en_US.json'

const initialState = fr

const langReducer = (state = initialState, { type, payload = null }) => {
    switch (type) {
    case LOCALE_CHANGED:
        switch (payload) {
        case fr.lang_code: return {
            ...state,
            ...fr,
        }

        case en.lang_code: return {
            ...state,
            ...en,
        }

        default: return state
        }

    default: return state
    }
}

export default langReducer
