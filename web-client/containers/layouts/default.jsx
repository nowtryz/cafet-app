import React from 'react'
import Helmet from 'react-helmet'
import { connect } from 'react-redux'
import cx from 'classnames'
import PropTypes from 'prop-types'
import { Redirect } from 'react-router-dom'
// creates a beautiful scrollbar
import PerfectScrollbar from 'perfect-scrollbar'
import 'perfect-scrollbar/css/perfect-scrollbar.css'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'

import appStyle from 'assets/jss/material-dashboard-pro-react/layouts/adminStyle'

import image from 'assets/img/sidebar-2.jpg'
import logo from 'assets/img/logo-white.svg'

import Footer from '../Footer'

var ps

class Layout extends React.Component {
    static propTypes = {
        children: PropTypes.oneOfType([
            PropTypes.arrayOf(PropTypes.element),
            PropTypes.element
        ]).isRequired,
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        fullScreenMaps: PropTypes.bool,
        displayFooter: PropTypes.bool,
        title: PropTypes.string.isRequired,
        isLogged: PropTypes.bool.isRequired,
        Sidebar: PropTypes.oneOfType([PropTypes.func, PropTypes.instanceOf(React.Component)]).isRequired,
        Navbar: PropTypes.oneOfType([PropTypes.func, PropTypes.instanceOf(React.Component)]).isRequired,
        routes: PropTypes.arrayOf(PropTypes.object).isRequired,
        lang: PropTypes.objectOf(PropTypes.any).isRequired
    }

    static defaultProps = {
        fullScreenMaps: false,
        displayFooter: true
    }

    state = {
        mobileOpen: false,
        miniActive: false,
        image: image,
        color: 'blue',
        bgColor: 'black',
        //hasImage: true,
        fixedClasses: 'dropdown'
    }

    mainPanel = React.createRef()

    componentDidMount = () => {
        const { isLogged } = this.props
        // if no user is logged, the layout won't be mounted as expected
        if (isLogged && navigator.platform.indexOf('Win') > -1) {
            ps = new PerfectScrollbar(this.mainPanel.current, {
                suppressScrollX: true,
                suppressScrollY: false
            })
            document.body.style.overflow = 'hidden'
        }
        window.addEventListener('resize', this.resizeFunction)
    }

    componentDidUpdate(prevProps) {
        const { mobileOpen } = this.state
        const { isLogged } = this.props

        if (isLogged && prevProps.history.location.pathname !== prevProps.location.pathname) {
            this.mainPanel.current.scrollTop = 0
            if (mobileOpen) {
                this.setState({ mobileOpen: false })
            }
        }
    }

    componentWillUnmount() {
        const { isLogged } = this.props
        // if no user is logged, the layout haven't been mounted as expected
        if (isLogged && navigator.platform.indexOf('Win') > -1) {
            ps.destroy()
        }
        window.removeEventListener('resize', this.resizeFunction)
    }

    sidebarMinimize = () => {
        const { miniActive } = this.state
        this.setState({ miniActive: !miniActive })
    }

    resizeFunction = () => {
        if (window.innerWidth >= 960) {
            this.setState({ mobileOpen: false })
        }
    }

    handleImageClick = image => {
        this.setState({ image: image })
    }

    handleColorClick = color => {
        this.setState({ color: color })
    }

    handleBgColorClick = bgColor => {
        this.setState({ bgColor: bgColor })
    }

    handleFixedClick = () => {
        const { fixedClasses } = this.state

        if (fixedClasses === 'dropdown') {
            this.setState({ fixedClasses: 'dropdown show' })
        } else {
            this.setState({ fixedClasses: 'dropdown' })
        }
    }

    handleDrawerToggle = () => {
        const { mobileOpen } = this.state
        this.setState({ mobileOpen: !mobileOpen })
    }

    render() {
        const {
            classes,
            fullScreenMaps,
            displayFooter,
            children,
            title,
            isLogged,
            Sidebar,
            Navbar,
            lang,
            routes,
            ...rest } = this.props
        const { miniActive, image, mobileOpen, color, bgColor } = this.state

        const mainPanel = cx(classes.mainPanel, {
            [classes.mainPanelSidebarMini]: miniActive,
            [classes.mainPanelWithPerfectScrollbar]: navigator.platform.indexOf('Win') > -1
        })

        if (!isLogged) return <Redirect to='/login' />

        return (
            <div className={classes.wrapper}>
                <Helmet title={lang[title] || title} />
                <Sidebar
                    routes={routes}
                    logoText="Cafet'"
                    logo={logo}
                    image={image}
                    handleDrawerToggle={this.handleDrawerToggle}
                    open={mobileOpen}
                    color={color}
                    bgColor={bgColor}
                    miniActive={miniActive}
                    {...rest}
                />
                <div className={mainPanel} ref={this.mainPanel}>
                    <Navbar
                        sidebarMinimize={this.sidebarMinimize}
                        miniActive={miniActive}
                        brandText={lang[title] || title}
                        handleDrawerToggle={this.handleDrawerToggle}
                        {...rest}
                    />
                    {/* On the /maps/full-screen-maps route we want the map to be on full screen - this is not possible if the content and conatiner classes are present because they have some paddings which would make the map smaller */}
                    {!fullScreenMaps ? (
                        <div className={classes.content}>
                            <div className={classes.container}>
                                {children}
                            </div>
                        </div>
                    ) : (
                        <div className={classes.map}>
                            {children}
                        </div>
                    )}
                    {displayFooter ? <Footer fluid /> : null}
                </div>
            </div>
        )
    }
}

const mapStateToProps = state => ({
    isLogged: state.user.user !== null
})

export default withStyles(appStyle)(connect(mapStateToProps)(Layout))
