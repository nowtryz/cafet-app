import React from 'react'
import Helmet from 'react-helmet'
import { connect } from 'react-redux'
import cx from 'classnames'
import PropTypes from 'prop-types'
import ReactRouterPropTypes from 'react-router-prop-types'
// creates a beautiful scrollbar
import PerfectScrollbar from 'perfect-scrollbar'
import 'perfect-scrollbar/css/perfect-scrollbar.css'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'

import appStyle from '@dashboard/assets/jss/material-dashboard-pro-react/layouts/adminStyle'

import image from '@dashboard/assets/img/sidebar-2.jpg'
import logo from '@dashboard/assets/img/logo-white.svg'

import {
    classes as classesPropType,
    children as childrenPropType
} from 'app-proptypes'
import _ from 'lang'
import Footer from '../Footer'

class DefaultLayout extends React.Component {
    static propTypes = {
        children: childrenPropType.isRequired,
        classes: classesPropType.isRequired,
        fullScreenMaps: PropTypes.bool,
        displayFooter: PropTypes.bool,
        title: PropTypes.string.isRequired,
        Sidebar: PropTypes.oneOfType([PropTypes.func, PropTypes.instanceOf(React.Component)]).isRequired,
        Navbar: PropTypes.oneOfType([PropTypes.func, PropTypes.instanceOf(React.Component)]).isRequired,
        routes: PropTypes.arrayOf(PropTypes.object).isRequired,
        organisation: PropTypes.string.isRequired,
        match: ReactRouterPropTypes.match.isRequired,
        location: ReactRouterPropTypes.location.isRequired,
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
    ps = null

    componentDidMount = () => {
        // if no user is logged, the layout won't be mounted as expected
        if (navigator.platform.indexOf('Win') > -1) {
            this.ps = new PerfectScrollbar(this.mainPanel.current, {
                suppressScrollX: true,
                suppressScrollY: false
            })
            document.body.style.overflow = 'hidden'
        }
        window.addEventListener('resize', this.resizeFunction)
    }

    componentDidUpdate(prevProps) {
        const { mobileOpen } = this.state

        if (prevProps.history.location.pathname !== prevProps.location.pathname) {
            this.mainPanel.current.scrollTop = 0
            if (mobileOpen) {
                this.setState({ mobileOpen: false })
            }
        }
    }

    componentWillUnmount() {
        if (this.ps && navigator.platform.indexOf('Win') > -1) {
            this.ps.destroy()
        }
        window.removeEventListener('resize', this.resizeFunction)
    }
    
    getPageTitle(routes) {
        const { location } = this.props
        const path = location.pathname
        return _(this.getTitleFromRoutes(routes, path))
    }

    getTitleFromRoutes(routes, path) {
        for (let i = 0; i < routes.length; i++) {
            const route = routes[i]
            if(route.path && path.match(new RegExp(route.path.replace(/:[^/?#]+/, '[^/?#]+') + '$'))) {
                return route.title
            }
            else if(route.items !== undefined) {
                const title = this.getTitleFromRoutes(route.items, path)
                if (title) return title
            }
        }
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
            Sidebar,
            Navbar,
            organisation,
            routes,
            ...rest } = this.props
        const { miniActive, image, mobileOpen, color, bgColor } = this.state

        const mainPanel = cx(classes.mainPanel, {
            [classes.mainPanelSidebarMini]: miniActive,
            [classes.mainPanelWithPerfectScrollbar]: navigator.platform.indexOf('Win') > -1
        })

        return (
            <div className={classes.wrapper}>
                <Helmet title={_(title)} />
                <Sidebar
                    routes={routes}
                    logoText={organisation}
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
                        brandText={this.getPageTitle(routes)}
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
    organisation: state.server.organisation
})

export default withStyles(appStyle)(connect(mapStateToProps)(DefaultLayout))
