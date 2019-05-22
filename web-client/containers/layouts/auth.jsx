import React from 'react'
import Helmet from 'react-helmet'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'
import { Redirect } from 'react-router-dom'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'

// core components

import pagesStyle from '@dashboard/assets/jss/material-dashboard-pro-react/layouts/authStyle'

import error from 'assets/img/clint-mckoy.jpg'
import Footer from '../Footer'
import AuthNavbar from '../navbars/AuthNavbar'

class Layout extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        children: PropTypes.oneOfType([
            PropTypes.arrayOf(PropTypes.element),
            PropTypes.element
        ]).isRequired,
        title: PropTypes.string.isRequired,
        bgImage: PropTypes.string,
        isLogged: PropTypes.bool.isRequired
    }

    static defaultProps = {
        bgImage: error
    }

    componentDidMount() {
        document.body.style.overflow = 'unset'
    }
    render() {
        const { classes, children, title, bgImage, isLogged, ...rest } = this.props

        if (isLogged) return <Redirect to='/' />

        return (
            <div>
                <Helmet title={title} />
                <AuthNavbar brandText={title} {...rest} />
                <div className={classes.wrapper}>
                    <div
                        className={classes.fullPage}
                        style={{ backgroundImage: 'url(' + bgImage + ')' }}
                    >
                        {children}
                        <Footer white />
                    </div>
                </div>
            </div>
        )
    }
}

const mapStateToProps = state => ({
    isLogged: state.user.user !== null
})

export default withStyles(pagesStyle)(connect(mapStateToProps)(Layout))