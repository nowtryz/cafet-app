import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'


const Message = ({ message }) =>
  <p>{message}</p>

Message.prototype = {
    message: PropTypes.string
}

const mapStateToProps = state => ({
  message: state.sample.message,
})

export default connect(mapStateToProps)(Message)