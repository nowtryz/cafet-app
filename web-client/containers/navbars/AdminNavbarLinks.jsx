import React from 'react'
import { connect } from 'react-redux'
import classNames from 'classnames'
import PropTypes from 'prop-types'
import { Link } from 'react-router-dom'
// import { Manager, Target, Popper } from "react-popper";

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'
import Hidden from '@material-ui/core/Hidden'

// @material-ui/icons
import Person from '@material-ui/icons/Person'
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
import NotificationControlMenu from '../menus/NotificationControlMenu'

class HeaderLinks extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        isAdmin: PropTypes.bool.isRequired,
    }

    render() {
        const { classes, isAdmin } = this.props

        const searchButton = classNames(classes.top, classes.searchButton)

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
                    classes={classes}
                    buttonProps={{
                        color: 'transparent',
                        'aria-label': 'Person',
                        justIcon: true,
                        className: classes.buttonLink,
                    }}
                >
                    <Language
                        className={classNames(classes.headerLinksSvg, classes.links)}
                    />
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
                <NotificationControlMenu classes={classes} />
                <UserControlMenu
                    classes={classes}
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

const style = (theme) => {
    const breakpoint = theme.breakpoints.down('sm')
    const defaultStyle = {
        ...adminNavbarLinksStyle(theme),
        fixJustify: {
            justifyContent: 'flex-start',
        },
        menuItem: {
            paddingRight: '30px',
            paddingLeft: '30px',
            '&:hover': {
                color: 'inherit',
            },
        },
    }

    // Override material dashboard style, as it uses !important modifiers
    defaultStyle.popperNav[breakpoint]['& > div'].marginLeft = '1.5rem'
    defaultStyle.popperNav[breakpoint]['& > div'].marginRight = '1.5rem'
    defaultStyle.popperNav[breakpoint]['& > div'].marginBottom = '5px !important'
    defaultStyle.popperNav[breakpoint]['& > div'].backgroundColor = undefined
    defaultStyle.popperNav[breakpoint]['& > div']['& ul li'] = undefined

    return defaultStyle
}

export default withStyles(style)(connect(mapStateToProps)(HeaderLinks))
