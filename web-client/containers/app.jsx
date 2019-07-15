import React from 'react'
import Helmet from 'react-helmet'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { createBrowserHistory } from 'history'
import { Router, Route, Switch, Redirect } from 'react-router-dom'

import AuthLayout from '@dashboard/layouts/Auth'
import RtlLayout from '@dashboard/layouts/RTL'
import DemoAdminLayout from '@dashboard/layouts/Admin'

import '@dashboard/assets/scss/material-dashboard-pro-react.scss?v=1.5.0'

import {
    loadServerConfig as loadServerConfigAction,
    grabUserInfo as grabUserInfoAction
} from 'actions'

import { lang as langPropType } from 'app-proptypes'

import Login from './auth/login'
import Register from './auth/register'
import { APP_NAME } from '../config'

import DashboardLayout from './layouts/dashboard'
import AdminLayout from './layouts/admin'

import adminRoutes from '../routes/admin'
import routes from '../routes/dashboard'

const hist = createBrowserHistory()

class App extends React.Component {
    static propTypes = {
        lang: langPropType.isRequired,
        loadServerConfig: PropTypes.func.isRequired,
        grabUserInfo: PropTypes.func.isRequired,
    }

    constructor(props) {
        super(props)
    }

    renderRoutes(routes, Layout, lang) {
        return routes.slice(0).reverse().map(route => {
            if (route.items) return this.renderRoutes(route.items, Layout, lang)
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

    renderRoutesTemp(routes, lang) {
        
        return routes.slice(0).reverse().map(route => {
            if (route.items) return this.renderRoutesTemp(route.items, lang)
            else {
                return (
                    <Route
                        exact
                        key={route.path}
                        path={route.path}
                        render={routeProps => (
                            <route.component {...routeProps} {...route.componentProps} />
                        )}
                    />
                )
            }
        })
    }

    render() {
        const { lang, isLogged } = this.props
        return (
            <React.Fragment>
                <Helmet
                    titleTemplate={`%s \xB7 ${APP_NAME}`}
                    defaultTitle={APP_NAME}
                    htmlAttributes={{ lang : lang.html_lang }}
                />
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
                        {!isLogged ? <Redirect to='/login' /> : null}
                        <Route
                            path='/dashboard'
                            render={routeProps => (
                                <DashboardLayout {...routeProps} lang={lang}>
                                    <Switch>
                                        {this.renderRoutesTemp(routes, lang)}
                                    </Switch>
                                </DashboardLayout>
                            )}
                        />
                        <Route
                            path='/admin'
                            render={routeProps => (
                                <AdminLayout {...routeProps} lang={lang}>
                                    <Switch>
                                        {this.renderRoutesTemp(adminRoutes, lang)}
                                    </Switch>
                                </AdminLayout>
                            )}
                        />
                        <Route path="/demo/rtl" component={RtlLayout} />
                        <Route path="/demo/auth" component={AuthLayout} />
                        <Route path="/demo/admin" component={DemoAdminLayout} />
                        <Redirect from="/demo/" to="/demo/admin/dashboard" />
                        <Redirect from='/' to='/dashboard' />
                    </Switch>
                </Router>
            </React.Fragment>
        )
    }
}

const mapStateToProps = state => ({
    isLogged: state.user.user !== null,
    lang: state.lang
})

export default connect(mapStateToProps, {
    loadServerConfig: loadServerConfigAction,
    grabUserInfo: grabUserInfoAction
})(App)