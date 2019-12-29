
// @material-ui/icons
import DashboardIcon from '@material-ui/icons/Dashboard'
import History from '@material-ui/icons/History'
import Timeline from '@material-ui/icons/Timeline'

import { isProd } from 'config'


import React from 'react'
// eslint-disable-next-line react/jsx-filename-extension
const BlankPage = () => (<p>this is a placeholder page</p>)

export default [
    {
        path: '/dashboard',
        title: 'Dashboard',
        id: 'dashboard',
        icon: DashboardIcon,
        component: BlankPage,
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
        display: 'Historique du compte',
        id: 'history',
        icon: History,
        component: BlankPage,
    },
    {
        path: '/dashboard/reloads',
        title: 'Reloads',
        id: 'reloads',
        // icon: Timer,
        mini: 'RE',
        hidden: isProd,
        component: BlankPage,
    },
]
