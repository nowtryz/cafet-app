import { CHANGE_LANG } from 'constants'

// eslint-disable-next-line import/prefer-default-export
export const changeLang = lang => dispach => dispach({
    type: CHANGE_LANG,
    payload: lang
})
