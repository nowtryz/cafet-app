import React from 'react'
import PropTypes from 'prop-types'

import routes from 'routes/admin'

import { children as childrenPropType } from 'app-proptypes'
import DefaultLayout from './default'
import AdminNavbar from '../navbars/AdminNavbar'
import AdminSidebar from '../sidebars/AdminSidebar'

const AdminLayout = ({
    children, fullScreenMaps, displayFooter, ...rest
}) => (
    <DefaultLayout
        fullScreenMaps={fullScreenMaps}
        displayFooter={displayFooter}
        Sidebar={AdminSidebar}
        Navbar={AdminNavbar}
        routes={routes}
        {...rest}
    >
        {children}
    </DefaultLayout>
)

AdminLayout.propTypes = {
    children: childrenPropType.isRequired,
    fullScreenMaps: PropTypes.bool,
    displayFooter: PropTypes.bool,
}

AdminLayout.defaultProps = {
    fullScreenMaps: false,
    displayFooter: true,
}

export default AdminLayout
