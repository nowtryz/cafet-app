import React from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import PropTypes from 'prop-types'
import classNames from 'classnames'
import Button from '@dashboard/components/CustomButtons/Button'
import ClickAwayListener from '@material-ui/core/ClickAwayListener'
import Grow from '@material-ui/core/Grow'
import Paper from '@material-ui/core/Paper'
import Popper from '@material-ui/core/Popper'
import MenuItem from '@material-ui/core/MenuItem'
import MenuList from '@material-ui/core/MenuList'
import Divider from '@material-ui/core/Divider'

import { logout as logoutAction } from '../../actions'
import Locale from '../Locale'
import { classesProptype } from '../../app-proptypes'

const UserControlMenu = ({
    classes, children, logout, buttonProps, ...rest
}) => {
    const [open, setOpen] = React.useState(false)
    const anchorEl = React.useRef(null)

    return (
        <div className={classes.managerClasses} {...rest}>
            <Button
                buttonRef={anchorEl}
                aria-owns={open ? 'menu-list-grow' : undefined}
                aria-haspopup="true"
                onClick={() => setOpen(!open)}
                {...buttonProps}
            >
                {children}
            </Button>
            <Popper
                open={open}
                anchorEl={anchorEl.current}
                transition
                disablePortal
                placement="bottom"
                className={classNames(
                    { [classes.popperClose]: !open },
                    classes.popperResponsive,
                    classes.popperNav,
                )}
            >
                {({ TransitionProps }) => (
                    <Grow
                        {...TransitionProps}
                        id="menu-list-grow"
                    >
                        <Paper>
                            <ClickAwayListener onClickAway={() => setOpen(false)}>
                                <MenuList>
                                    <MenuItem className={classes.menuItem} component={Link} to="/dashboard/profile">
                                        <Locale>Profile</Locale>
                                    </MenuItem>
                                    <MenuItem className={classes.menuItem} component={Link} to="/dashboard/settings">
                                        <Locale>Settings</Locale>
                                    </MenuItem>
                                    <Divider />
                                    <MenuItem className={classes.menuItem} onClick={logout}>
                                        <Locale>Logout</Locale>
                                    </MenuItem>
                                </MenuList>
                            </ClickAwayListener>
                        </Paper>
                    </Grow>
                )}
            </Popper>
        </div>
    )
}

UserControlMenu.propTypes = {
    children: PropTypes.oneOfType([
        PropTypes.arrayOf(PropTypes.element),
        PropTypes.element,
    ]).isRequired,
    logout: PropTypes.func.isRequired,
    buttonProps: PropTypes.objectOf(PropTypes.any),
    classes: classesProptype.isRequired,
}

UserControlMenu.defaultProps = {
    buttonProps: {},
}

export default connect(null, {
    logout: logoutAction,
})(UserControlMenu)
