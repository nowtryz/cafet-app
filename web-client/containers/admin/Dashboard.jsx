import React from 'react'
import Component from 'views/Dashboard/Dashboard'
import AdminLayout from '../layouts/admin'

const Dashboard = props => (
    <AdminLayout title='test' {...props}>
        <Component {...props} />
    </AdminLayout>
)

export default Dashboard