import store from '../reducers'
import { changeLocale } from '../actions'
import { isProd } from '../config'

export const setLocalization = (lang) => store.dispatch(changeLocale(lang))
export const getLocalizedText = (text, namespace = 'default') => {
    if (!text) return text
    const lang = store.getState().lang || {}
    const ns = lang[namespace] || {}
    if (isProd) return ns[text] || text
    if (ns[text] !== undefined) return ns[text]

    // eslint-disable-next-line no-console
    console.warn(`Missing translation for "${text}" in "${namespace}"!`)
    return text
}

export default getLocalizedText
