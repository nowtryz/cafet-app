import { hot } from 'react-hot-loader/root'
import React from 'react'
import Helmet from 'react-helmet'
import { useSelector } from 'react-redux'
import { history as historyPropTypes } from 'react-router-prop-types'
import {
    Router, Route, Switch, Redirect,
} from 'react-router-dom'

import '@dashboard/assets/scss/material-dashboard-pro-react.scss'

import Login from './auth/login'
import Register from './auth/register'
import DashboardLayout from './layouts/dashboard'
import AdminLayout from './layouts/admin'
import { APP_NAME } from '../config'
import adminRoutes from '../routes/admin'
import dashboardRoutes from '../routes/dashboard'


const renderRoutes = (routes, lang) => routes.slice(0).reverse().map((route) => {
    if (route.items) return renderRoutes(route.items, lang)
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

const App = ({ history }) => {
    const [isLogged, lang] = useSelector((state) => [
        state.user.user !== null,
        state.lang,
    ])

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
                                    {renderRoutes(dashboardRoutes, lang)}
                                </Switch>
                            </DashboardLayout>
                        )}
                    />
                    <Route
                        path="/admin"
                        render={(routeProps) => (
                            <AdminLayout {...routeProps} lang={lang}>
                                <Switch>
                                    {renderRoutes(adminRoutes, lang)}
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

App.propTypes = {
    history: historyPropTypes.isRequired,
}

export default hot(App)
