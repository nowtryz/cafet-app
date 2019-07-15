import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { vsprintf } from 'sprintf-js'

import { lang as langPropType } from 'app-proptypes'
import { getLocalizedText as _ } from 'lang'

const Locale = ({children, lang, ns, variables, ...rest}) => {
    lang // used to subscribe to lang change
    return (
        <React.Fragment>
            {variables ? vsprintf(_(children, ns), variables) : vsprintf(_(children, ns), rest)}
        </React.Fragment>
    )
}

Locale.propTypes = {
    lang: langPropType.isRequired,
    children: PropTypes.string.isRequired,
    ns: PropTypes.string,
    variables: PropTypes.oneOfType([
        PropTypes.arrayOf(PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.number
        ])),
        PropTypes.objectOf(PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.number
        ])),
        PropTypes.arrayOf(PropTypes.objectOf(PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.number
        ])))
    ])
}

Locale.defaultProps = {
    ns: undefined,
    variables: undefined
}

const mapStateToProps = state => ({
    lang: state.lang
})

export default connect(mapStateToProps)(Locale)