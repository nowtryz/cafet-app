import PropTypes from 'prop-types'

export const lang = PropTypes.objectOf(PropTypes.oneOfType([
    PropTypes.string,
    PropTypes.object
]))

export const children = PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.element),
    PropTypes.element
])

export const classes = PropTypes.objectOf(PropTypes.string)