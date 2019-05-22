import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

const Locale = ({children, lang}) => {
    return (
        <React.Fragment>
            {lang[children]}
        </React.Fragment>
    )
}

Locale.propTypes = {
    lang: PropTypes.objectOf(PropTypes.any).isRequired,
    children: PropTypes.string.isRequired
}

const mapStateToProps = state => ({
    lang: state.lang
})

export default connect(mapStateToProps)(Locale)