import React, { useState } from 'react'
import PropTypes from 'prop-types'
import classNames from 'classnames'
import { useSelector } from 'react-redux'
import { NavLink } from 'react-router-dom'
import md5 from 'md5'
// @material-ui/core components
import List from '@material-ui/core/List'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'
import Collapse from '@material-ui/core/Collapse'

import _ from '../../lang'

const useOpenState = () => {
    const [isOpen, setOpen] = useState(false)
    return [isOpen, () => setOpen(!isOpen)]
}

const SidebarUserWrapper = ({ classes, color }) => {
    const { userDisplay, gravatar } = useSelector((state) => ({
        userDisplay: `${state.user.user.firstName} ${state.user.user.familyName}`,
        gravatar: `https://www.gravatar.com/avatar/${md5(state.user.user.email.trim())}?s=50&d=retro`,
    }))
    const [isOpen, switchOpen] = useOpenState()

    return (
        <div className={classes.userWrapperClass}>
            <div
                className={classNames(classes.photo, classes[color])}
                style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                }}
            >
                <img
                    style={{
                        height: '100%',
                        width: '100%',
                    }}
                    src={gravatar}
                    alt="gravatar"
                    title="gravatar"
                />
            </div>
            <List className={classes.list}>
                <ListItem className={classNames(classes.item, classes.userItem)}>
                    <NavLink
                        to="#"
                        className={classNames(classes.itemLink, classes.userCollapseButton)}
                        onClick={switchOpen}
                    >
                        <ListItemText
                            primary={userDisplay}
                            secondary={(
                                <b
                                    className={classNames(
                                        classes.caret,
                                        classes.userCaret,
                                        { [classes.caretActive]: isOpen },
                                    )}
                                />
                            )}
                            disableTypography
                            className={classNames(classes.itemText, classes.userItemText)}
                        />
                    </NavLink>
                    <Collapse in={isOpen} unmountOnExit>
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

SidebarUserWrapper.propTypes = {
    classes: PropTypes.objectOf(PropTypes.string).isRequired,
    color: PropTypes.oneOf([
        'white',
        'red',
        'orange',
        'green',
        'blue',
        'purple',
        'rose',
    ]).isRequired,
}

export default SidebarUserWrapper
