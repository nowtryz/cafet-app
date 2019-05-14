import React from 'react'
import PropTypes from 'prop-types';
import { connect } from 'react-redux'

import { doSomething } from '../action/doSomething'


const Button = ({ label, handleClick }) =>
  <button onClick={handleClick}>{label}</button>

Button.prototype = {
    label: PropTypes.string,
    handleClick: PropTypes.Function
}

const mapStateToProps = () => ({
  label: 'Say hello',
})

const mapDispatchToProps = {
  handleClick: doSomething('Hello!'),
}

export default connect(mapStateToProps, mapDispatchToProps)(Button)