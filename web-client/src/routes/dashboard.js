
// @material-ui/icons
import DashboardIcon from '@material-ui/icons/Dashboard'
import HistoryIcon from '@material-ui/icons/History'
import Timeline from '@material-ui/icons/Timeline'
import { CreditCard } from '@material-ui/icons'

import { isProd } from '../config'
import Dashboard from '../containers/dashboard/Dashboard'
import History from '../containers/dashboard/History'

// eslint-disable-next-line import/order
import React from 'react'
// eslint-disable-next-line react/jsx-filename-extension
const BlankPage = () => (<p>this is a placeholder page</p>)

export default [
    {
        path: '/dashboard',
        title: 'Dashboard',
        id: 'dashboard',
        icon: DashboardIcon,
        component: Dashboard,
    },
    {
        path: '/dashboard/profile',
        title: 'Profile',
        id: 'profile',
        hidden: true,
        component: BlankPage,
    },
    {
        path: '/dashboard/password',
        title: 'password',
        id: 'password',
        hidden: true,
        component: BlankPage,
    },
    {
        path: '/dashboard/settings',
        title: 'Preferences',
        id: 'preferences',
        hidden: true,
        component: BlankPage,
    },
    {
        path: '/dashboard/stats',
        title: 'Statistics',
        id: 'statistics',
        icon: Timeline,
        hidden: isProd,
        component: BlankPage,
    },
    {
        path: '/dashboard/history',
        title: 'History',
        id: 'history',
        icon: HistoryIcon,
        component: History,
    },
    {
        path: '/dashboard/reloads',
        title: 'Reloads',
        id: 'reloads',
        icon: CreditCard,
        hidden: isProd,
        component: BlankPage,
    },
]
