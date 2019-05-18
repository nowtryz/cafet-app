import React, { Fragment } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

import {
    login as loginAcion,
    logout as logoutAction
} from '../actions'

const handleClick = login => () => login('nowtryz', 'admin')


const Button = ({ label, action }) =>
    <button type="button" onClick={action}>{label}</button>

Button.propTypes = {
    label: PropTypes.string.isRequired,
    action: PropTypes.func.isRequired
}

const Buttons = ({login, logout}) => (
    <Fragment>
        <Button label="login" action={handleClick(login)} />
        <Button label="logout" action={logout} />
    </Fragment>
)

Buttons.propTypes = {
    login: PropTypes.func.isRequired,
    logout: PropTypes.func.isRequired
}

const mapDispatchToProps = {
    login: loginAcion,
    logout: logoutAction
}

export default connect(null, mapDispatchToProps)(Buttons)