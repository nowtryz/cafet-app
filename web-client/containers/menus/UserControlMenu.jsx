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
import { withStyles } from '@material-ui/core/styles'
import Divider from '@material-ui/core/Divider'

import adminNavbarLinksStyle from '@dashboard/assets/jss/material-dashboard-pro-react/components/adminNavbarLinksStyle'

import { logout as logoutAction } from 'actions'
import Locale from '../Locale'


const styles = (theme) => ({
    ...adminNavbarLinksStyle(theme),
    menuItem: {
        paddingRight: '30px',
        paddingLeft: '30px',
        '&:hover': {
            color: 'inherit',
        },
    },
})

class UserControlMenu extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        children: PropTypes.oneOfType([
            PropTypes.arrayOf(PropTypes.element),
            PropTypes.element,
        ]).isRequired,
        buttonProps: PropTypes.objectOf(PropTypes.any),
    }

    static defaultProps = {
        buttonProps: {},
    }

    state = {
        open: false,
    }

    handleToggle = () => {
        this.setState((state) => ({ open: !state.open }))
    }

    handleClose = (event) => {
        if (this.anchorEl.contains(event.target)) {
            return
        }

        this.setState({ open: false })
    }

    render() {
        const {
            classes, children, logout, buttonProps, ...rest
        } = this.props
        const { open } = this.state

        return (
            <div {...rest}>
                <Button
                    buttonRef={(node) => {
                        this.anchorEl = node
                    }}
                    aria-owns={open ? 'menu-list-grow' : undefined}
                    aria-haspopup="true"
                    onClick={this.handleToggle}
                    {...buttonProps}
                >
                    {children}
                </Button>
                <Popper
                    open={open}
                    anchorEl={this.anchorEl}
                    transition
                    disablePortal
                    placement="bottom"
                    className={classNames({
                        [classes.popperClose]: !open,
                        [classes.pooperResponsive]: true,
                        [classes.pooperNav]: true,
                    })}
                >
                    {({ TransitionProps }) => (
                        <Grow
                            {...TransitionProps}
                            id="menu-list-grow"
                        >
                            <Paper>
                                <ClickAwayListener onClickAway={this.handleClose}>
                                    <MenuList>
                                        <MenuItem className={classes.menuItem} component={Link} to="/dashboard/profile"><Locale>Profile</Locale></MenuItem>
                                        <MenuItem className={classes.menuItem} component={Link} to="/dashboard/settings"><Locale>Settings</Locale></MenuItem>
                                        <Divider />
                                        <MenuItem className={classes.menuItem} onClick={logout}><Locale>Logout</Locale></MenuItem>
                                    </MenuList>
                                </ClickAwayListener>
                            </Paper>
                        </Grow>
                    )}
                </Popper>
            </div>
        )
    }
}

export default withStyles(styles)(connect(null, {
    logout: logoutAction,
})(UserControlMenu))
