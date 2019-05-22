import React from 'react'
import Component from 'views/Dashboard/Dashboard'
import DashboardLayout from '../layouts/dashboard'

const Dashboard = props => (
    <DashboardLayout title='test' {...props}>
        <Component {...props} />
    </DashboardLayout>
)

export default Dashboard