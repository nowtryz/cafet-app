import React from 'react'
import { connect } from 'react-redux'
import classNames from 'classnames'
import PropTypes from 'prop-types'
import { Link } from 'react-router-dom'
// import { Manager, Target, Popper } from "react-popper";

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'
import MenuItem from '@material-ui/core/MenuItem'
import MenuList from '@material-ui/core/MenuList'
import ClickAwayListener from '@material-ui/core/ClickAwayListener'
import Paper from '@material-ui/core/Paper'
import Grow from '@material-ui/core/Grow'
import Hidden from '@material-ui/core/Hidden'
import Popper from '@material-ui/core/Popper'

// @material-ui/icons
import Person from '@material-ui/icons/Person'
import Notifications from '@material-ui/icons/Notifications'
import Dashboard from '@material-ui/icons/Dashboard'
import Search from '@material-ui/icons/Search'
import Build from '@material-ui/icons/Build'
import Language from '@material-ui/icons/Language'

// core components
import CustomInput from '@dashboard/components/CustomInput/CustomInput'
import Button from '@dashboard/components/CustomButtons/Button'

import adminNavbarLinksStyle from '@dashboard/assets/jss/material-dashboard-pro-react/components/adminNavbarLinksStyle'

import LangControlMenu from '../menus/LangControlMenu'
import UserControlMenu from '../menus/UserControlMenu'
import Locale from '../Locale'

class HeaderLinks extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        isAdmin: PropTypes.bool.isRequired,
    }

    state = {
        open: false,
    }

    handleClick = () => {
        this.setState((state) => ({ open: !state.open }))
    }

    handleClose = () => {
        this.setState({ open: false })
    }

    render() {
        const { classes, isAdmin } = this.props
        const { open } = this.state

        const searchButton = classNames(classes.top, classes.searchButton)
        const dropdownItem = classNames(classes.dropdownItem, classes.primaryHover)

        return (
            <div>
                <CustomInput
                    formControlProps={{
                        className: classNames(classes.top, classes.search),
                    }}
                    inputProps={{
                        placeholder: 'Search',
                        inputProps: {
                            'aria-label': 'Search',
                            className: classes.searchInput,
                        },
                    }}
                />
                <Button
                    color="white"
                    aria-label="edit"
                    justIcon
                    round
                    className={searchButton}
                >
                    <Search
                        className={`${classes.headerLinksSvg} ${classes.searchIcon}`}
                    />
                </Button>
                <LangControlMenu
                    className={classes.managerClasses}
                    buttonProps={{
                        color: 'transparent',
                        'aria-label': 'Person',
                        justIcon: true,
                        className: classes.buttonLink,
                    }}
                >
                    <Language className={classNames(classes.headerLinksSvg, classes.links)} />
                    <Hidden mdUp implementation="css">
                        <span className={classes.linkText}>
                            <Locale>Language</Locale>
                        </span>
                    </Hidden>
                </LangControlMenu>
                {isAdmin ? (
                    <div className={classes.managerClasses}>
                        <Button
                            color="transparent"
                            simple
                            aria-label="Dashboard"
                            justIcon
                            className={classNames(classes.fixJustify, classes.buttonLink)}
                            component={Link}
                            to="/admin"
                        >
                            <Build className={classNames(classes.headerLinksSvg, classes.links)} />
                            <Hidden mdUp implementation="css">
                                <span className={classes.linkText}>
                                    <Locale>Admin Panel</Locale>
                                </span>
                            </Hidden>
                        </Button>
                    </div>
                ) : null}
                <div className={classes.managerClasses}>
                    <Button
                        color="transparent"
                        simple
                        aria-label="Dashboard"
                        justIcon
                        className={classNames(classes.fixJustify, classes.buttonLink)}
                        component={Link}
                        to="/dashboard"
                    >
                        <Dashboard className={classNames(classes.headerLinksSvg, classes.links)} />
                        <Hidden mdUp implementation="css">
                            <span className={classes.linkText}>
                                <Locale>Dashboard</Locale>
                            </span>
                        </Hidden>
                    </Button>
                </div>
                <div className={classes.managerClasses}>
                    <Button
                        color="transparent"
                        justIcon
                        aria-label="Notifications"
                        aria-owns={open ? 'menu-list' : null}
                        aria-haspopup="true"
                        onClick={this.handleClick}
                        className={classes.buttonLink}
                        buttonRef={(node) => {
                            this.anchorEl = node
                        }}
                    >
                        <Notifications className={classNames(classes.headerLinksSvg, classes.links)} />
                        <span className={classes.notifications}>5</span>
                        <Hidden mdUp implementation="css">
                            <span className={classes.linkText}>
                                <Locale>Notifications</Locale>
                            </span>
                        </Hidden>
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
                                id="menu-list"
                                style={{ transformOrigin: '0 0 0' }}
                            >
                                <Paper className={classes.dropdown}>
                                    <ClickAwayListener onClickAway={this.handleClose}>
                                        <MenuList role="menu">
                                            <MenuItem
                                                onClick={this.handleClose}
                                                className={dropdownItem}
                                            >
                                                Mike John responded to your email
                                            </MenuItem>
                                            <MenuItem
                                                onClick={this.handleClose}
                                                className={dropdownItem}
                                            >
                                                You have 5 new tasks
                                            </MenuItem>
                                            <MenuItem
                                                onClick={this.handleClose}
                                                className={dropdownItem}
                                            >
                                                You&apos;re now friend with Andrew
                                            </MenuItem>
                                            <MenuItem
                                                onClick={this.handleClose}
                                                className={dropdownItem}
                                            >
                                                Another Notification
                                            </MenuItem>
                                            <MenuItem
                                                onClick={this.handleClose}
                                                className={dropdownItem}
                                            >
                                                Another One
                                            </MenuItem>
                                        </MenuList>
                                    </ClickAwayListener>
                                </Paper>
                            </Grow>
                        )}
                    </Popper>
                </div>
                <UserControlMenu
                    className={classes.managerClasses}
                    buttonProps={{
                        color: 'transparent',
                        'aria-label': 'Person',
                        justIcon: true,
                        className: classes.buttonLink,
                    }}
                >
                    <Person className={classNames(classes.headerLinksSvg, classes.links)} />
                    <Hidden mdUp implementation="css">
                        <span className={classes.linkText}>
                            <Locale>Profile</Locale>
                        </span>
                    </Hidden>
                </UserControlMenu>
            </div>
        )
    }
}

const mapStateToProps = (state) => ({
    isAdmin: state.user.user !== undefined && state.user.user.group.id >= 4,
    lang: state.lang,
})

const style = (theme) => ({
    ...adminNavbarLinksStyle(theme),
    fixJustify: {
        justifyContent: 'flex-start',
    },
})

export default withStyles(style)(connect(mapStateToProps)(HeaderLinks))
