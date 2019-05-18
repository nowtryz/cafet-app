import React from 'react'
import Helmet from 'react-helmet'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { createBrowserHistory } from 'history'
import { Router, Route, Switch, Redirect } from 'react-router-dom'

import AuthLayout from 'layouts/Auth'
import RtlLayout from 'layouts/RTL'
import AdminLayout from 'layouts/Admin'

import Login from 'containers/pages/login'
import { APP_NAME } from '../config'

import 'assets/scss/material-dashboard-pro-react.scss?v=1.5.0'

const hist = createBrowserHistory()

const App = ({lang}) => (
    <React.Fragment>
        <Helmet titleTemplate={`%s | ${APP_NAME}`} defaultTitle={APP_NAME} />
        <Router history={hist}>
            <Switch>
                <Route 
                    path='/login' 
                    component={(routeProps) => (
                        <Login {...routeProps} lang={lang} />
                    )}
                />
                <Route 
                    path='/register' 
                    component={(routeProps) => (
                        <Login {...routeProps} lang={lang} />
                    )}
                />
                <Route 
                    path='/lock' 
                    component={(routeProps) => (
                        <Login {...routeProps} lang={lang} />
                    )}
                />
                <Route path='/rtl' component={RtlLayout} />
                <Route path='/auth' component={AuthLayout} />
                <Route path='/admin' component={AdminLayout} />
                <Redirect from='/' to='/admin/dashboard' />
            </Switch>
        </Router>
    </React.Fragment>
)

App.propTypes = {
    lang: PropTypes.PropTypes.objectOf(PropTypes.string).isRequired
}

const mapStateToProps = state => ({
    lang: state.lang
})

export default connect(mapStateToProps)(App)