import {
    arrayOf, element, object, objectOf, oneOfType, string,
} from 'prop-types'

export const lang = objectOf(oneOfType([
    string,
    object,
]))

export const children = oneOfType([
    arrayOf(element),
    element,
])

export const classes = objectOf(string)
