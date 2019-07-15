import Dashboard from 'containers/admin/Dashboard'
import Users from 'containers/admin/Users'
import UserPage from 'containers/admin/UserPage'

// @material-ui/icons
import DashboardIcon from '@material-ui/icons/Dashboard'
import CreditCard from '@material-ui/icons/CreditCard'
import Build from '@material-ui/icons/Build'
import Group from '@material-ui/icons/Group'
import Whatshot from '@material-ui/icons/Whatshot'
import Timer from '@material-ui/icons/Timer'
import Security from '@material-ui/icons/Security'
import MultilineChart from '@material-ui/icons/MultilineChart'
import Store from '@material-ui/icons/Store'
import Timeline from '@material-ui/icons/Timeline'
import Explore from '@material-ui/icons/Explore'
import SettingsApplications from '@material-ui/icons/SettingsApplications'
import FolderOpen from '@material-ui/icons/FolderOpen'


import React from 'react'
// eslint-disable-next-line react/jsx-filename-extension
const BlankPage = () => (<p>this is a placeholder page</p>)

export default [
    {
        path: '/admin',
        title: 'Dashboard',
        id: 'dashboard',
        icon: DashboardIcon,
        component: Dashboard,
        layoutProps: {},
        componentProps: {}
    },
    {
        title: 'Monitoring',
        id: 'monitoring',
        icon: MultilineChart,
        items: [
            {
                path: '/admin/system-info',
                title: 'System info',
                id: 'system infos',
                icon: Whatshot,
                component: BlankPage,
            },
            {
                path: '/admin/routines',
                title: 'Routines',
                id: 'routines',
                icon: Timer,
                component: BlankPage,
            },
            {
                path: '/admin/storage',
                title: 'Storage and Backups',
                id: 'used_space',
                icon: FolderOpen,
                component: BlankPage,
            },
        ],

    },
    {
        title: 'Overview',
        id: 'overview',
        icon: Explore,
        items: [
            {
                path: '/admin/users',
                title: 'Users',
                id: 'users',
                icon: Group,
                component: Users,
            },
            {
                hidden: true,
                path: '/admin/users/:id',
                title: 'User Page',
                id: 'user page',
                component: UserPage,
            },
            {
                path: '/admin/stats',
                title: 'Statistics',
                id: 'stats',
                icon: Timeline,
                component: BlankPage,
            },
            {
                path: '/admin/stocks',
                title: 'Product Stocks',
                id: 'stocks',
                icon: Store,
                component: BlankPage,
            },
            {
                path: '/admin/reports',
                title: 'Reports',
                id: 'reports',
                icon: Security,
                component: BlankPage,
            },
        ],
    },
    {
        title: 'Settings',
        id: 'settings',
        icon: SettingsApplications,
        items: [
            {
                path: '/admin/preferences',
                title: 'Preferences',
                id: 'preferences',
                icon: Build,
                component: BlankPage
            },
            {
                path: '/admin/payment-methods',
                title: 'Payment',
                icon: CreditCard,
                component: BlankPage
            }
        ]
    }
]