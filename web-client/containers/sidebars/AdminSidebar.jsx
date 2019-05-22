import React from 'react'
import PropTypes from 'prop-types'
import cx from 'classnames'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'
import Drawer from '@material-ui/core/Drawer'
import Hidden from '@material-ui/core/Hidden'

// core components

import sidebarStyle from 'assets/jss/material-dashboard-pro-react/components/sidebarStyle'

import SidebarWrapper from './SidebarWrapper'
import SidebarUserWrapper from './SidebarUserWrapper'
import SidebarRoutes from './SidebarRoutes'
import AdminNavbarLinks from '../navbars/AdminNavbarLinks'

class Sidebar extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        bgColor: PropTypes.oneOf(['white', 'black', 'blue']),
        color: PropTypes.oneOf([
            'white',
            'red',
            'orange',
            'green',
            'blue',
            'purple',
            'rose'
        ]).isRequired,
        logo: PropTypes.string.isRequired,
        logoText: PropTypes.string.isRequired,
        image: PropTypes.string.isRequired,
        routes: PropTypes.arrayOf(PropTypes.object).isRequired,
        miniActive: PropTypes.bool,
        handleDrawerToggle: PropTypes.func.isRequired,
        open: PropTypes.bool.isRequired
    }

    static defaultProps = {
        bgColor: 'blue',
        miniActive: false
    }

    state = {
        miniActive: true
    }

    processClasses() {
        const {
            classes,
            bgColor,
            miniActive
        } = this.props
        const { miniActive:miniState } = this.state

        return ({
            ...classes,
            itemText: cx(classes.itemText, {
                [classes.itemTextMini]: miniActive && miniState
            }),
            collapseItemText: cx(classes.collapseItemText, {
                [classes.collapseItemTextMini]: miniActive && miniState
            }),
            userWrapperClass: cx(classes.user, {
                [classes.whiteAfter]: bgColor === 'white'
            }),
            logoNormal: cx(classes.logoNormal, {
                [classes.logoNormalSidebarMini]: miniActive && miniState
            }),
            logoClasses: cx(classes.logo, {
                [classes.whiteAfter]: bgColor === 'white'
            }),
            drawerPaper: cx(classes.drawerPaper, {
                [classes.drawerPaperMini]:
                miniActive && miniState
            }),
            sidebarWrapper: cx (classes.sidebarWrapper, {
                [classes.drawerPaperMini]: miniActive && miniState,
                [classes.sidebarWrapperWithPerfectScrollbar]: navigator.platform.indexOf('Win') > -1
            })
        })
    }

    render() {
        const {
            logo,
            image,
            logoText,
            routes,
            bgColor,
            color,
            handleDrawerToggle,
            open,
            miniActive
        } = this.props
        const { miniActive:miniState } = this.state
        const classes = this.processClasses()

        var brand = (
            <div className={classes.logo}>
                <a href="https://www.creative-tim.com" className={classes.logoMini}>
                    <img src={logo} alt="logo" className={classes.img} />
                </a>
                <a href="https://www.creative-tim.com" className={classes.logoNormal}>
                    {logoText}
                </a>
            </div>
        )

        return (
            <div ref="mainPanel">
                <Hidden mdUp implementation="css">
                    <Drawer
                        variant="temporary"
                        anchor='right'
                        open={open}
                        classes={{
                            paper: cx(classes.drawerPaper, classes[bgColor + 'Background'])
                        }}
                        onClose={handleDrawerToggle}
                        ModalProps={{
                            keepMounted: true // Better open performance on mobile.
                        }}
                    >
                        {brand}
                        <SidebarWrapper className={classes.sidebarWrapper}>
                            <SidebarUserWrapper classes={classes} color={color} />
                            <AdminNavbarLinks />
                            <SidebarRoutes
                                classes={classes}
                                miniActive={miniActive && miniState}
                                routes={routes}
                                color={color}
                            />
                        </SidebarWrapper>
                        {image !== undefined ? (
                            <div
                                className={classes.background}
                                style={{ backgroundImage: 'url(' + image + ')' }}
                            />
                        ) : null}
                    </Drawer>
                </Hidden>
                <Hidden smDown implementation="css">
                    <Drawer
                        onMouseOver={() => this.setState({ miniActive: false })}
                        onFocus={() => this.setState({ miniActive: false })}
                        onMouseOut={() => this.setState({ miniActive: true })}
                        onBlur={() => this.setState({ miniActive: true })}
                        anchor='left'
                        variant="permanent"
                        open
                        classes={{
                            paper: cx(classes.drawerPaper, classes[bgColor + 'Background'])
                        }}
                    >
                        {brand}
                        <SidebarWrapper className={classes.sidebarWrapper}>
                            <SidebarUserWrapper classes={classes} color={color} />
                            <SidebarRoutes
                                classes={classes}
                                miniActive={miniActive && miniState}
                                routes={routes}
                                color={color}
                            />
                        </SidebarWrapper>
                        {image !== undefined ? (
                            <div
                                className={classes.background}
                                style={{ backgroundImage: 'url(' + image + ')' }}
                            />
                        ) : null}
                    </Drawer>
                </Hidden>
            </div>
        )
    }
}

export default withStyles(sidebarStyle)(Sidebar)
