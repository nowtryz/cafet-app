import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'


const Message = ({ message }) =>
    <p>{message}</p>

Message.propTypes = {
    message: PropTypes.string.isRequired
}

const mapStateToProps = state => ({
    message: state.user.message,
})

export default connect(mapStateToProps)(Message)