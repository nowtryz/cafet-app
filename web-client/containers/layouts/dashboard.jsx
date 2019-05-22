import React from 'react'
import PropTypes from 'prop-types'

import routes from 'routes/dashboard'

import DefaultLayout from './default'
import AdminNavbar from '../navbars/AdminNavbar'
import AdminSidebar from '../sidebars/AdminSidebar'

const Layout = ({ children, fullScreenMaps, displayFooter, title, ...rest}) => (
    <DefaultLayout
        fullScreenMaps={fullScreenMaps}
        displayFooter={displayFooter}
        title={title}
        Sidebar={AdminSidebar}
        Navbar={AdminNavbar}
        routes={routes}
        {...rest}
    >
        {children}
    </DefaultLayout>
)

Layout.propTypes = {
    children: PropTypes.oneOfType([
        PropTypes.arrayOf(PropTypes.element),
        PropTypes.element
    ]).isRequired,
    fullScreenMaps: PropTypes.bool,
    displayFooter: PropTypes.bool,
    title: PropTypes.string.isRequired
}

Layout.defaultProps = {
    fullScreenMaps: false,
    displayFooter: true
}

export default Layout