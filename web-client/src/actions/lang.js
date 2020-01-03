import moment from 'moment'
import { LOCALE_CHANGED } from '../constants'

// eslint-disable-next-line import/prefer-default-export
export const changeLocale = (lang) => (dispatch) => {
    moment.locale(lang)
    dispatch({
        type: LOCALE_CHANGED,
        payload: lang,
    })
}
