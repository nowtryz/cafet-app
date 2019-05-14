import React from 'react'
import PropTypes from 'prop-types';
import { connect } from 'react-redux'

import { doSomething } from '../actions'

const handleClick = clickHandler => () => clickHandler('Hello')


const Button = ({ label, clickHandler }) =>
  <button onClick={handleClick(clickHandler)}>{label}</button>

Button.prototype = {
    label: PropTypes.string,
    handleClick: PropTypes.Function
}

const mapStateToProps = () => ({
  label: 'Say hello',
})

const mapDispatchToProps = {
  clickHandler: doSomething,
}

export default connect(mapStateToProps, mapDispatchToProps)(Button)