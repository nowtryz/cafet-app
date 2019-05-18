import '@babel/polyfill'

import React from 'react'
import { connect } from 'react-redux'
import mergeClasses from 'classnames'
import PropTypes from 'prop-types'
import { NavLink } from 'react-router-dom'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'
import AppBar from '@material-ui/core/AppBar'
import Toolbar from '@material-ui/core/Toolbar'
import Hidden from '@material-ui/core/Hidden'
import Drawer from '@material-ui/core/Drawer'
import List from '@material-ui/core/List'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'

// @material-ui/icons
import Menu from '@material-ui/icons/Menu'

// core components
import Button from 'components/CustomButtons/Button'

import links from 'routes/auth'

import authNavbarStyle from 'assets/jss/material-dashboard-pro-react/components/authNavbarStyle'

class AuthNavbar extends React.Component {
    state = {
        open: false
    }

    static getDerivedStateFromProps(props, state) {
        let open = state.open

        if ( state.previousPathname === undefined || props.location.pathname !== state.previousPathname ) {
            open = false
        }

        return {
            ...state,
            previousPathname: props.location.pathname,
            open
        }
    }

    handleDrawerToggle = () => {
        this.setState(prevState => {
            return {open: !prevState.open}
        })
    }

    // verifies if routeName is the one active (in browser input)
    activeRoute(routeName) {
        const { location } = this.props
        return location.pathname.indexOf(routeName) > -1 ? true : false
    }

    renderLinks() {
        const { classes, lang } = this.props

        return (Object.keys(links).map(key => {
            const link = links[key]
            return (
                <ListItem className={classes.listItem} key={link}>
                    <NavLink
                        to={link.path}
                        className={mergeClasses(classes.navLink, {
                            [classes.navLinkActive]: this.activeRoute(link.path)
                        })}
                    >
                        <link.icon className={classes.listItemIcon} />
                        <ListItemText
                            primary={lang[link.title]}
                            disableTypography
                            className={classes.listItemText}
                        />
                    </NavLink>
                </ListItem>
            )
        }))
    }

    render() {
        const { classes, color, brandText } = this.props
        const { open } = this.state
        const appBarClasses = mergeClasses({
            [' ' + classes[color]]: color
        })

        return (
            <AppBar position='static' className={classes.appBar + appBarClasses}>
                <Toolbar className={classes.container}>
                    <Hidden smDown>
                        <div className={classes.flex}>
                            <Button href='#' className={classes.title} color='transparent'>
                                {brandText}
                            </Button>
                        </div>
                    </Hidden>
                    <Hidden mdUp>
                        <div className={classes.flex}>
                            <Button href='#' className={classes.title} color='transparent'>
                    MD Pro React
                            </Button>
                        </div>
                    </Hidden>
                    <Hidden smDown>
                        <List className={classes.list}>
                            {this.renderLinks()}
                        </List>
                    </Hidden>
                    <Hidden mdUp>
                        <Button
                            className={classes.sidebarButton}
                            color='transparent'
                            justIcon
                            aria-label='open drawer'
                            onClick={this.handleDrawerToggle}
                        >
                            <Menu />
                        </Button>
                    </Hidden>
                    <Hidden mdUp>
                        <Hidden mdUp>
                            <Drawer
                                variant='temporary'
                                anchor="right"
                                open={open}
                                classes={{
                                    paper: classes.drawerPaper
                                }}
                                onClose={this.handleDrawerToggle}
                                ModalProps={{
                                    keepMounted: true // Better open performance on mobile.
                                }}
                            >
                                {this.renderLinks()}
                            </Drawer>
                        </Hidden>
                    </Hidden>
                </Toolbar>
            </AppBar>
        )
    }
}

AuthNavbar.defaultProps = {
    color: 'primary',
    brandText: ''
}

AuthNavbar.propTypes = {
    classes: PropTypes.PropTypes.objectOf(PropTypes.object()).isRequired,
    lang: PropTypes.PropTypes.objectOf(PropTypes.string).isRequired,
    color: PropTypes.oneOf(['primary', 'info', 'success', 'warning', 'danger']),
    brandText: PropTypes.string,
    location: PropTypes.shape({
        pathname: PropTypes.string.isRequired,
    }).isRequired
}

const mapStateToProps = state => ({
    lang: state.lang
})

export default withStyles(authNavbarStyle)(connect(mapStateToProps)(AuthNavbar))
