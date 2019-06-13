import React from 'react'
import PropTypes from 'prop-types'
import classNames from 'classnames'
import { connect } from 'react-redux'
import { NavLink } from 'react-router-dom'

// @material-ui/core components
import List from '@material-ui/core/List'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'
import Collapse from '@material-ui/core/Collapse'
import Person from '@material-ui/icons/Person'

import _ from 'lang'

class SidebarUserWrapper extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        userDisplay: PropTypes.string.isRequired,
        color: PropTypes.oneOf([
            'white',
            'red',
            'orange',
            'green',
            'blue',
            'purple',
            'rose'
        ]).isRequired,
    }
    
    state = {
        openAvatar: false
    }

    openAvatar = () => {
        this.setState(state => ({openAvatar: !state.openAvatar}))
    }

    render() {
        const { classes, userDisplay, color } = this.props
        const { openAvatar } = this.state

        return (
            <div className={classes.userWrapperClass}>
                <div
                    className={classNames(classes.photo, classes[color])}
                    style={{
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center'
                    }}
                >
                    <Person />
                </div>
                <List className={classes.list}>
                    <ListItem className={classNames(classes.item,classes.userItem)}>
                        <NavLink
                            to="#"
                            className={classNames(classes.itemLink, classes.userCollapseButton)}
                            onClick={this.openAvatar}
                        >
                            <ListItemText
                                primary={userDisplay}
                                secondary={(<b className={classNames(classes.caret, classes.userCaret, {[classes.caretActive]: openAvatar})} />)}
                                disableTypography
                                className={classNames(classes.itemText, classes.userItemText)}
                            />
                        </NavLink>
                        <Collapse in={openAvatar} unmountOnExit>
                            <List className={classNames(classes.list, classes.collapseList)}>
                                <ListItem className={classes.collapseItem}>
                                    <NavLink
                                        to="/dashboard/profile"
                                        className={classNames(classes.itemLink, classes.userCollapseLinks)}
                                    >
                                        <span className={classes.collapseItemMini}>
                                            MP
                                        </span>
                                        <ListItemText
                                            primary={_('My Profile')}
                                            disableTypography
                                            className={classes.collapseItemText}
                                        />
                                    </NavLink>
                                </ListItem>
                                <ListItem className={classes.collapseItem}>
                                    <NavLink
                                        to="/dashboard/password"
                                        className={classNames(classes.itemLink, classes.userCollapseLinks)}
                                    >
                                        <span className={classes.collapseItemMini}>
                                            C
                                        </span>
                                        <ListItemText
                                            primary={_('Change Password')}
                                            disableTypography
                                            className={classes.collapseItemText}
                                        />
                                    </NavLink>
                                </ListItem>
                                <ListItem className={classes.collapseItem}>
                                    <NavLink
                                        to="/dashboard/settings"
                                        className={classNames(classes.itemLink, classes.userCollapseLinks)}
                                    >
                                        <span className={classes.collapseItemMini}>
                                        S
                                        </span>
                                        <ListItemText
                                            primary={_('Settings')}
                                            disableTypography
                                            className={classes.collapseItemText}
                                        />
                                    </NavLink>
                                </ListItem>
                            </List>
                        </Collapse>
                    </ListItem>
                </List>
            </div>
        )
    }
}

const mapStateToProps = state => ({
    userDisplay: `${state.user.user.firstname} ${state.user.user.familyName}`
})

export default connect(mapStateToProps)(SidebarUserWrapper)