import { LOCALE_CHANGED } from 'constants'

// eslint-disable-next-line import/prefer-default-export
export const changeLocale = lang => dispach => dispach({
    type: LOCALE_CHANGED,
    payload: lang
})
