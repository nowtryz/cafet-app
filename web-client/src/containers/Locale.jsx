import React from 'react'
import PropTypes from 'prop-types'
import { vsprintf } from 'sprintf-js'

import _ from '../lang'

/**
 * An element to translate sentences and words
 * @param children a string to be translated from language files
 * @param ns the namespace to take translations from, or *default* namespace if not provided
 * @param variables arguments used by [sprintf.js](https://github.com/alexei/sprintf.js/blob/master/README.md)
 * @param rest
 * @returns React.ReactElement
 * @example
 *  <Local name="fixed value">
 *      Some text defined in languages files with %(name)s
 *  </Local>
 * @example
 *  <Local variables={['string 1', 'string 2']}>
 *      Some text defined in languages files with %s and more %s
 *  </Local>
 */
const Locale = ({
    children, ns, variables, ...rest
}) => (
    <>
        {variables ? vsprintf(_(children, ns), variables) : vsprintf(_(children, ns), rest)}
    </>
)

Locale.propTypes = {
    children: PropTypes.string.isRequired,
    ns: PropTypes.string,
    variables: PropTypes.oneOfType([
        PropTypes.arrayOf(PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.number,
        ])),
        PropTypes.objectOf(PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.number,
        ])),
        PropTypes.arrayOf(PropTypes.objectOf(PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.number,
        ]))),
    ]),
}

Locale.defaultProps = {
    ns: undefined,
    variables: undefined,
}

export default Locale
