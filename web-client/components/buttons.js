import React, { Fragment } from 'react'
import PropTypes from 'prop-types';
import { connect } from 'react-redux'

import {
  login as loginAcion,
  logout as logoutAction
} from '../actions'

const handleClick = login => () => login('nowtryz', 'admin')


const Button = ({ label, action }) =>
  <button onClick={action}>{label}</button>

Button.prototype = {
    label: PropTypes.string,
    action: PropTypes.Function
}

const Buttons = ({login, logout}) =>
<Fragment>
  <Button label="login" action={handleClick(login)} />
  <Button label="logout" action={logout} />
</Fragment>

Button.prototype = {
  login: PropTypes.Function,
  logout: PropTypes.Function
}

const mapDispatchToProps = {
  login: loginAcion,
  logout: logoutAction
}

export default connect(null, mapDispatchToProps)(Buttons)