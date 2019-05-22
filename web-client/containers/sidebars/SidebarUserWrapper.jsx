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

import avatar from 'assets/img/faces/avatar.jpg'

class SidebarUserWrapper extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        lang: PropTypes.objectOf(PropTypes.oneOfType([PropTypes.string, PropTypes.object])).isRequired
    }
    
    state = {
        openAvatar: false
    }

    openAvatar = () => {
        this.setState(state => ({openAvatar: !state.openAvatar}))
    }

    render() {
        const { classes, lang } = this.props
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
                            onClick={this.openAvatar}
                        >
                            <ListItemText
                                primary='Tania Andrew'
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
                                            primary={lang['My Profile']}
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
                                            EP
                                        </span>
                                        <ListItemText
                                            primary={lang['Change Password']}
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
                                            primary={lang.Settings}
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
    lang: state.lang
})

export default connect(mapStateToProps)(SidebarUserWrapper)