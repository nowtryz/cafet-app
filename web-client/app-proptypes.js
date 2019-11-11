import {
    arrayOf, bool, element, exact, number, object, objectOf, oneOfType, shape, string,
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

export const permissions = objectOf(bool)

export const group = shape({
    id: number.isRequired,
    name: string.isRequired,
    permissions: permissions.isRequired,
})

export const calendar = shape({
    year: number.isRequired,
    month: number.isRequired,
    day: number.isRequired,
    hour: number.isRequired,
    mins: number.isRequired,
    secs: number.isRequired,
})

export const user = shape({
    id: number.isRequired,
    pseudo: string.isRequired,
    phone: string.isRequired,
    email: string.isRequired,
    familyName: string.isRequired,
    firstName: string.isRequired,
    permissions: permissions.isRequired,
    group: group.isRequired,
    customer_id: number,
    signin_count: number.isRequired,
    last_signin: calendar.isRequired,
    registration: calendar.isRequired,
    mail_preferences: exact({
        payment_notice: bool.isRequired,
        reload_notice: bool.isRequired,
        reload_request: bool.isRequired,
    }),
})

export {
    lang as langProptype,
    children as childrenProptype,
    classes as classesProptype,
    permissions as permissionsProptype,
    group as groupProptype,
    calendar as calendarProptype,
    user as userProptype,
}
