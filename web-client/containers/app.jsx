import { hot } from 'react-hot-loader/root'
import React from 'react'
import Helmet from 'react-helmet'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { history as historyPropTypes } from 'react-router-prop-types'
import {
    Router, Route, Switch, Redirect,
} from 'react-router-dom'

import '@dashboard/assets/scss/material-dashboard-pro-react.scss?v=1.5.0'

import {
    loadServerConfig as loadServerConfigAction,
    grabUserInfo as grabUserInfoAction,
} from 'actions'

import { lang as langPropType } from 'app-proptypes'

import Login from './auth/login'
import Register from './auth/register'
import { APP_NAME } from '../config'

import DashboardLayout from './layouts/dashboard'
import AdminLayout from './layouts/admin'

import adminRoutes from '../routes/admin'
import dashboardRoutes from '../routes/dashboard'

class App extends React.Component {
    static propTypes = {
        lang: langPropType.isRequired,
        loadServerConfig: PropTypes.func.isRequired,
        grabUserInfo: PropTypes.func.isRequired,
        isLogged: PropTypes.bool.isRequired,
        history: historyPropTypes.isRequired,
    }

    renderRoutes(routes, lang) {
        return routes.slice(0).reverse().map((route) => {
            if (route.items) return this.renderRoutes(route.items, lang)

            return (
                <Route
                    exact
                    key={route.path}
                    path={route.path}
                    render={(routeProps) => (
                        <route.component {...routeProps} {...route.componentProps} />
                    )}
                />
            )
        })
    }

    render() {
        const { history, lang, isLogged } = this.props

        return (
            <>
                <Helmet
                    titleTemplate={`%s \xB7 ${APP_NAME}`}
                    defaultTitle={APP_NAME}
                    htmlAttributes={{ lang: lang.html_lang }}
                />
                <Router history={history}>
                    <Switch>
                        <Route
                            path="/login"
                            render={(routeProps) => (
                                <Login {...routeProps} lang={lang} />
                            )}
                        />
                        <Route
                            path="/register"
                            render={(routeProps) => (
                                <Register {...routeProps} lang={lang} />
                            )}
                        />
                        <Route
                            path="/lock"
                            render={(routeProps) => (
                                <Login {...routeProps} lang={lang} />
                            )}
                        />
                        {!isLogged ? <Redirect to="/login" /> : null}
                        <Route
                            path="/dashboard"
                            render={(routeProps) => (
                                <DashboardLayout {...routeProps} lang={lang}>
                                    <Switch>
                                        {this.renderRoutes(dashboardRoutes, lang)}
                                    </Switch>
                                </DashboardLayout>
                            )}
                        />
                        <Route
                            path="/admin"
                            render={(routeProps) => (
                                <AdminLayout {...routeProps} lang={lang}>
                                    <Switch>
                                        {this.renderRoutes(adminRoutes, lang)}
                                    </Switch>
                                </AdminLayout>
                            )}
                        />
                        <Redirect from="/" to="/dashboard" />
                    </Switch>
                </Router>
            </>
        )
    }
}

const mapStateToProps = (state) => ({
    isLogged: state.user.user !== null,
    lang: state.lang,
})

export default hot(connect(mapStateToProps, {
    loadServerConfig: loadServerConfigAction,
    grabUserInfo: grabUserInfoAction,
})(App))
