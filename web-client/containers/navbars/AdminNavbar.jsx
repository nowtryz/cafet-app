import React from 'react'
import PropTypes from 'prop-types'
import cx from 'classnames'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'
import AppBar from '@material-ui/core/AppBar'
import Toolbar from '@material-ui/core/Toolbar'
import Hidden from '@material-ui/core/Hidden'

// material-ui icons
import Menu from '@material-ui/icons/Menu'
import MoreVert from '@material-ui/icons/MoreVert'
import ViewList from '@material-ui/icons/ViewList'
import Person from '@material-ui/icons/Person'

// core components
import Button from '@dashboard/components/CustomButtons/Button'

import adminNavbarStyle from '@dashboard/assets/jss/material-dashboard-pro-react/components/adminNavbarStyle'
import adminNavbarLinksStyle from '@dashboard/assets/jss/material-dashboard-pro-react/components/adminNavbarLinksStyle'
import AdminNavbarLinks from './AdminNavbarLinks'

const AdminNavbar = ({ classes, color, brandText, miniActive, sidebarMinimize, handleDrawerToggle, ...props }) => {
    return (
        <AppBar className={cx(classes.appBar, { [classes.color]: color})}>
            <Toolbar className={classes.container}>
                <Hidden smDown implementation='css'>
                    <div className={classes.sidebarMinimize}>
                        {miniActive ? (
                            <Button
                                justIcon
                                round
                                color='white'
                                onClick={sidebarMinimize}
                            >
                                <ViewList className={classes.sidebarMiniIcon} />
                            </Button>
                        ) : (
                            <Button
                                justIcon
                                round
                                color='white'
                                onClick={sidebarMinimize}
                            >
                                <MoreVert className={classes.sidebarMiniIcon} />
                            </Button>
                        )}
                    </div>
                </Hidden>
                <div className={classes.flex}>
                    {/* Here we create navbar brand, based on route name */}
                    <Button href='#' className={classes.title} color='transparent'>
                        {brandText}
                    </Button>
                </div>
                <Hidden smDown implementation='css'>
                    <AdminNavbarLinks />
                </Hidden>
                <Hidden mdUp implementation='css'>
                    <Button
                        className={classes.appResponsive}
                        color='transparent'
                        justIcon
                        aria-label='open drawer'
                        onClick={handleDrawerToggle}
                    >
                        <Menu />
                    </Button>
                </Hidden>
            </Toolbar>
        </AppBar>
    )
}

AdminNavbar.propTypes = {
    classes: PropTypes.objectOf(PropTypes.string).isRequired,
    color: PropTypes.oneOf(['primary', 'info', 'success', 'warning', 'danger']),
    brandText: PropTypes.string.isRequired
}

AdminNavbar.defaultProps = {
    color: null
}

const style = theme => ({
    ...adminNavbarStyle(theme),
    ...adminNavbarLinksStyle(theme)
})

export default withStyles(style)(AdminNavbar)
