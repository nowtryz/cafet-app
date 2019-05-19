import React from 'react'
import PropTypes from 'prop-types'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'

// core components
import Footer from 'components/Footer/Footer'

import pagesStyle from 'assets/jss/material-dashboard-pro-react/layouts/authStyle'

import error from 'assets/img/clint-mckoy.jpg'
import AuthNavbar from '../navbars/AuthNavbar'

class Layout extends React.Component {
    componentDidMount() {
        document.body.style.overflow = 'unset'
    }
    render() {
        const { classes, children, title, bgImage, ...rest } = this.props
        return (
            <div>
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

Layout.defaultProps = {
    bgImage: error
}

Layout.propTypes = {
    classes: PropTypes.objectOf(PropTypes.string).isRequired,
    children: PropTypes.oneOfType([
        PropTypes.arrayOf(PropTypes.element),
        PropTypes.element
    ]).isRequired,
    title: PropTypes.string.isRequired,
    bgImage: PropTypes.string
}

export default withStyles(pagesStyle)(Layout)