import { CHANGE_LANG } from 'constants'
import * as fr from '../lang/fr_FR'
import * as en from '../lang/en_US'

const initialState = fr

const sampleReducer = (state = initialState, { type, payload = null }) => {
    switch (type) {
    case CHANGE_LANG: switch (payload) {
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

export default sampleReducer