import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

import { lang as langPropType } from 'app-proptypes'
import { getLocalizedText as _ } from 'lang'

const Locale = ({children, lang, ns}) => {
    lang // used to subscribe to lang change
    return (
        <React.Fragment>
            {_(children, ns) || children}
        </React.Fragment>
    )
}

Locale.propTypes = {
    lang: langPropType.isRequired,
    children: PropTypes.string.isRequired,
    ns: PropTypes.string
}

Locale.defaultProps = {
    ns: undefined
}

const mapStateToProps = state => ({
    lang: state.lang
})

export default connect(mapStateToProps)(Locale)