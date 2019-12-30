import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { vsprintf } from 'sprintf-js'

import { langProptype } from '../app-proptypes'
import _ from '../lang'

/**
 * An element to translate sentences and words
 * @param children a string to be translated from language files
 * @param lang the lang grabbed from app's state
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
    children, lang, ns, variables, ...rest
}) => (
    <>
        {variables ? vsprintf(_(children, ns), variables) : vsprintf(_(children, ns), rest)}
    </>
)

Locale.propTypes = {
    lang: langProptype.isRequired,
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

const mapStateToProps = (state) => ({
    lang: state.lang, // used to subscribe to lang change
})

export default connect(mapStateToProps)(Locale)
