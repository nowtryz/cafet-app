import store from 'reducers'
import { changeLocale } from 'actions'

export { default as fr } from './fr_FR'
export { default as en } from './en_US'

export const setLocalization = (lang) => store.dispatch(changeLocale(lang))
export const getLocalizedText = (text, namespace = 'default') => {
    const lang = store.getState().lang || {}
    const ns = lang[namespace] || {}
    return ns[text] || text
}

export default getLocalizedText
