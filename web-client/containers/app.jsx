import React from 'react'
import Helmet from 'react-helmet'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { createBrowserHistory } from 'history'
import { Router, Route, Switch, Redirect } from 'react-router-dom'

import AuthLayout from '@dashboard/layouts/Auth'
import RtlLayout from '@dashboard/layouts/RTL'
import DemoAdminLayout from '@dashboard/layouts/Admin'

import '../material-dashboard/assets/scss/material-dashboard-pro-react.scss?v=1.5.0'

import Login from 'containers/auth/login'
import Register from 'containers/auth/register'
import { APP_NAME } from '../config'

import DashboardLayout from './layouts/dashboard'
import AdminLayout from './layouts/admin'

import adminRoutes from '../routes/admin'
import routes from '../routes/dashboard'

const hist = createBrowserHistory()

const renderRoutes = (routes, lang, Layout) => {
    return routes.slice(0).reverse().map(route => {
        if (route.items) return renderRoutes(route.items, lang, Layout)
        else {
            return (
                <Route
                    exact
                    key={route.path}
                    path={route.path}
                    render={routeProps => (
                        <Layout {...routeProps} {...route.layoutProps} title={route.title} lang={lang}>
                            <route.component {...routeProps} {...route.componentProps} />
                        </Layout>
                    )}
                />
            )
        }
    })
}

const App = ({lang}) => (
    <React.Fragment>
        <Helmet titleTemplate={`%s | ${APP_NAME}`} defaultTitle={APP_NAME} />
        <Router history={hist}>
            <Switch>
                <Route 
                    path='/login' 
                    render={(routeProps) => (
                        <Login {...routeProps} lang={lang} />
                    )}
                />
                <Route 
                    path='/register' 
                    render={(routeProps) => (
                        <Register {...routeProps} lang={lang} />
                    )}
                />
                <Route 
                    path='/lock' 
                    render={(routeProps) => (
                        <Login {...routeProps} lang={lang} />
                    )}
                />
                {renderRoutes(routes, lang, DashboardLayout)}
                {renderRoutes(adminRoutes, lang, AdminLayout)}
                <Route path="/demo/rtl" component={RtlLayout} />
                <Route path="/demo/auth" component={AuthLayout} />
                <Route path="/demo/admin" component={DemoAdminLayout} />
                <Redirect from="/demo/" to="/demo/admin/dashboard" />
                <Redirect from='/' to='/dashboard' />
            </Switch>
        </Router>
    </React.Fragment>
)

App.propTypes = {
    lang: PropTypes.PropTypes.objectOf(PropTypes.any).isRequired
}

const mapStateToProps = state => ({
    lang: state.lang
})

export default connect(mapStateToProps)(App)