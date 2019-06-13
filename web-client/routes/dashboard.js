

// @material-ui/icons
import DashboardIcon from '@material-ui/icons/Dashboard'
import History from '@material-ui/icons/History'
import ShoppingCart from '@material-ui/icons/ShoppingCart'
//import Timer from '@material-ui/icons/Timer'
import Face from '@material-ui/icons/Face'
import Timeline from '@material-ui/icons/Timeline'
import AttachMoney from '@material-ui/icons/AttachMoney'


import React from 'react'
// eslint-disable-next-line react/jsx-filename-extension
const BlankPage = () => (<p>this is a placeholder page</p>)
// eslint-disable-next-line react/jsx-filename-extension
const Test = () => (<p>this is a test page</p>)

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
        title: 'My Account',
        id: 'my account',
        icon: Face,
        items: [
            {
                path: '/dashboard/expenses',
                title: 'Expenses',
                id: 'expenses',
                icon: ShoppingCart,
                component: BlankPage,
            },
            {
                path: '/dashboard/stats',
                title: 'Statistics',
                id: 'statistics',
                icon: Timeline,
                component: BlankPage,
            },
        ],

    },
    {
        title: 'My balance',
        id: 'my balance',
        icon: AttachMoney,
        items: [
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
                //icon: Timer,
                mini: 'RE',
                component: BlankPage,
            },
        ],
    }
]