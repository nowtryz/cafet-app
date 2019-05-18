import React from 'react';
import ReactDOM from 'react-dom';
import { createBrowserHistory } from 'history';
import { Router, Route, Switch, Redirect } from 'react-router-dom';

import AuthLayout from './material-dashboard/layouts/Auth.jsx';
import RtlLayout from './material-dashboard/layouts/RTL.jsx';
import AdminLayout from './material-dashboard/layouts/Admin.jsx';

import './material-dashboard/assets/scss/material-dashboard-pro-react.scss?v=1.5.0';

const hist = createBrowserHistory();

const App = () =>
  <Router history={hist}>
    <Switch>
      <Route path='/rtl' component={RtlLayout} />
      <Route path='/auth' component={AuthLayout} />
      <Route path='/admin' component={AdminLayout} />
      <Redirect from='/' to='/admin/dashboard' />
    </Switch>
  </Router>

export default App