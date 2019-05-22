import React from 'react'
import PropTypes from 'prop-types'
import classNames from 'classnames'
import { NavLink } from 'react-router-dom'

// @material-ui/core components
import List from '@material-ui/core/List'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'
import Collapse from '@material-ui/core/Collapse'

import avatar from 'assets/img/faces/avatar.jpg'

class SidebarUserWrapper extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired
    }
    
    state = {
        openAvatar: false
    }

    openAvatar () {
        this.setState(state => ({openAvatar: !state.openAvatar}))
    }

    render() {
        const { classes } = this.props
        const { openAvatar } = this.state

        return (
            <div className={classes.userWrapperClass}>
                <div className={classes.photo}>
                    <img src={avatar} className={classes.avatarImg} alt="..." />
                </div>
                <List className={classes.list}>
                    <ListItem className={classNames(classes.item,classes.userItem)}>
                        <NavLink
                            to="#"
                            className={classNames(classes.itemLink, classes.userCollapseButton)}
                            onClick={() => this.openCollapse('openAvatar')}
                        >
                            <ListItemText
                                primary='Tania Andrew'
                                secondary={(<b className={classNames(classes.caret, classes.userCaret, {[classes.caretActive]: openAvatar})} />)}
                                disableTypography
                                className={classes.itemText + ' ' + classes.userItemText}
                            />
                        </NavLink>
                        <Collapse in={openAvatar} unmountOnExit>
                            <List className={classes.list + ' ' + classes.collapseList}>
                                <ListItem className={classes.collapseItem}>
                                    <NavLink
                                        to="#"
                                        className={
                                            classes.itemLink + ' ' + classes.userCollapseLinks
                                        }
                                    >
                                        <span className={classes.collapseItemMini}>
                                            MP
                                        </span>
                                        <ListItemText
                                            primary='My Profile'
                                            disableTypography
                                            className={classes.collapseItemText}
                                        />
                                    </NavLink>
                                </ListItem>
                                <ListItem className={classes.collapseItem}>
                                    <NavLink
                                        to="#"
                                        className={
                                            classes.itemLink + ' ' + classes.userCollapseLinks
                                        }
                                    >
                                        <span className={classes.collapseItemMini}>
                                            EP
                                        </span>
                                        <ListItemText
                                            primary='Edit Profile'
                                            disableTypography
                                            className={classes.collapseItemText}
                                        />
                                    </NavLink>
                                </ListItem>
                                <ListItem className={classes.collapseItem}>
                                    <NavLink
                                        to="#"
                                        className={
                                            classes.itemLink + ' ' + classes.userCollapseLinks
                                        }
                                    >
                                        <span className={classes.collapseItemMini}>
                                        S
                                        </span>
                                        <ListItemText
                                            primary='Settings'
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

export default SidebarUserWrapper