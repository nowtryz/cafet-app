import React from 'react'
import PropTypes from 'prop-types'
import cx from 'classnames'

import List from '@material-ui/core/List'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'
import Collapse from '@material-ui/core/Collapse'
import Icon from '@material-ui/core/Icon'
import BlurOn from '@material-ui/icons/BlurOn'
import { NavLink } from 'react-router-dom'

import _ from 'lang'

class SidebarRoutes extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        miniActive: PropTypes.bool,
        routes: PropTypes.arrayOf(PropTypes.object).isRequired,
        color: PropTypes.oneOf([
            'white',
            'red',
            'orange',
            'green',
            'blue',
            'purple',
            'rose'
        ]).isRequired
    }

    static defaultProps = {
        miniActive: false
    }

    static getDerivedStateFromProps(props, state) {
        return SidebarRoutes.getCollapseStates(props.routes, state)
    }

    // this creates the intial state of this component based on the collapse routes
    // that it gets through this.props.routes
    static getCollapseStates = (routes, state) => {
        routes.forEach(route => {
            if (route.items !== undefined) {
                state[route.id] = state[route.id] ||SidebarRoutes.souldBeOpen(route.items)
                this.getCollapseStates(route.items, state)
            }
        })

        return state
    }

    // this verifies if any of the collapses should be default opened on a rerender of this component
    static souldBeOpen(routes) {
        for (let i = 0; i < routes.length; i++) {
            if (routes[i].items !== undefined && SidebarRoutes.souldBeOpen(routes[i].items)) {
                return true
            } else if (window.location.href.indexOf(routes[i].path) !== -1) {
                return true
            }
        }
        return false
    }

    state = {}

    openCollapse(collapse) {
        this.setState(state => ({
            [collapse]: !state[collapse]
        }))

    }

    collapseAll() {
        this.setState(state => {
            const newState = {}
            Object.keys(state).forEach(v => newState[v] = false)
            return newState
        })
    }

    processClasses() {
        const { classes, miniActive } = this.props

        return ({
            ...classes,
            itemText: cx(classes.itemText, {
                [classes.itemTextMini]: miniActive
            }),
            collapseItemText: cx(classes.collapseItemText, {
                [classes.collapseItemTextMini]: miniActive
            })
        })
    }

    renderLinks(routes, classes) {
        return routes.map(route => {
            if (route.redirect || route.hidden) {
                return null
            }
            if (route.items !== undefined) {
                return this.renderGroup(route, classes)
            }
            else {
                return this.renderLink(route, classes)
            }
        })
    }

    renderGroup(route, classes) {
        const isCollapseItem = route.items === undefined
        const { [route.id]:collapseState } = this.state

        const navLinkClasses= cx(classes.itemLink, {
            [classes.collapseActive]: SidebarRoutes.souldBeOpen(route.items)
        })

        return (
            <ListItem
                key={route.id}
                className={isCollapseItem ? classes.collapseItem : classes.item}
            >
                <NavLink
                    to="#"
                    className={navLinkClasses}
                    onClick={e => {
                        e.preventDefault()
                        this.openCollapse(route.id)
                    }}
                >
                    {this.renderIcon(route, classes)}
                    <ListItemText
                        primary={_(route.title)}
                        secondary={<b className={cx(classes.caret, {[classes.caretActive]: collapseState})} />}
                        disableTypography
                        className={isCollapseItem ? classes.collapseItemText : classes.itemText}
                    />
                </NavLink>
                <Collapse in={collapseState} unmountOnExit>
                    <List className={cx(classes.list, classes.collapseList)}>
                        {this.renderLinks(route.items, classes)}
                    </List>
                </Collapse>
            </ListItem>
        )

    }

    renderLink(route, classes) {
        const { color } = this.props
        const isCollapseItem = route.items !== undefined

        return (
            <ListItem key={route.path} className={isCollapseItem ? classes.collapseItem : classes.item}>
                <NavLink
                    to={route.path}
                    exact
                    onClick={() => this.collapseAll()}
                    className={isCollapseItem ? classes.collapseItemLink: classes.itemLink}
                    activeClassName={classes[color]}
                >
                    {this.renderIcon(route, classes)}
                    <ListItemText
                        primary={_(route.title)}
                        disableTypography
                        className={isCollapseItem ? classes.collapseItemText : classes.itemText}
                    />
                </NavLink>
            </ListItem>
        )
    }

    renderIcon(route, classes) {
        if (route.icon !== undefined && typeof route.icon === 'string') {
            return <Icon className={classes.itemIcon}>{route.icon}</Icon>
        } else if (route.icon !== undefined) {
            return <route.icon className={classes.itemIcon} />
        } else if (route.mini) {
            return <span className={classes.collapseItemMini}>{route.mini}</span>
        } else {
            return <BlurOn className={classes.itemIcon} />
        }
    }

    render() {
        const { routes, classes } = this.props
        return (
            <List className={classes.list}>
                {this.renderLinks(routes, this.processClasses(classes))}
            </List>
        )
    }
}

export default SidebarRoutes
