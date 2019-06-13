import { LOCALE_CHANGED } from 'constants'
import fr from '../lang/fr_FR'
import en from '../lang/en_US'

const initialState = fr

const langReducer = (state = initialState, { type, payload = null }) => {
    switch (type) {
    case LOCALE_CHANGED:
        switch (payload) {
        case fr.lang: return {
            ...state,
            ...fr
        }

        case en.lang: return {
            ...state,
            ...en
        }

        default: return state
        }

    default: return state
    }
}

export default langReducer