import React from 'react'
import Button from '@dashboard/components/CustomButtons/Button'
import classNames from 'classnames'
import Hidden from '@material-ui/core/Hidden'
import Notifications from '@material-ui/icons/Notifications'
import Popper from '@material-ui/core/Popper'
import Grow from '@material-ui/core/Grow'
import Paper from '@material-ui/core/Paper'
import ClickAwayListener from '@material-ui/core/ClickAwayListener'
import MenuList from '@material-ui/core/MenuList'
import MenuItem from '@material-ui/core/MenuItem'
import Locale from '../Locale'
import { classesProptype } from '../../app-proptypes'

const NotificationControlMenu = ({ classes }) => {
    const [open, setOpen] = React.useState(false)
    const [anchorEl, setAnchorEl] = React.useState(null)
    const dropdownItem = classNames(classes.dropdownItem, classes.primaryHover)

    return (
        <div className={classes.managerClasses}>
            <Button
                color="transparent"
                justIcon
                aria-label="Notifications"
                aria-owns={open ? 'menu-list' : null}
                aria-haspopup="true"
                onClick={() => setOpen(!open)}
                className={classes.buttonLink}
                buttonRef={setAnchorEl}
            >
                <Notifications className={classNames(classes.headerLinksSvg, classes.links)} />
                <span className={classes.notifications}>5</span>
                <Hidden mdUp implementation="css">
                    <span className={classes.linkText}>
                        <Locale>Notifications</Locale>
                    </span>
                </Hidden>
            </Button>
            <Popper
                open={open}
                anchorEl={anchorEl}
                transition
                disablePortal
                placement="bottom"
                className={classNames(
                    { [classes.popperClose]: !open },
                    classes.popperResponsive,
                    classes.popperNav,
                )}
            >
                {({ TransitionProps }) => (
                    <Grow
                        {...TransitionProps}
                        id="menu-list"
                        style={{ transformOrigin: '0 0 0' }}
                    >
                        <Paper className={classes.dropdown}>
                            <ClickAwayListener onClickAway={() => setOpen(false)}>
                                <MenuList role="menu">
                                    <MenuItem
                                        onClick={() => setOpen(false)}
                                        className={dropdownItem}
                                    >
                                        Mike John responded to your email
                                    </MenuItem>
                                    <MenuItem
                                        onClick={() => setOpen(false)}
                                        className={dropdownItem}
                                    >
                                        You have 5 new tasks
                                    </MenuItem>
                                    <MenuItem
                                        onClick={() => setOpen(false)}
                                        className={dropdownItem}
                                    >
                                        You&apos;re now friend with Andrew
                                    </MenuItem>
                                    <MenuItem
                                        onClick={() => setOpen(false)}
                                        className={dropdownItem}
                                    >
                                        Another Notification
                                    </MenuItem>
                                    <MenuItem
                                        onClick={() => setOpen(false)}
                                        className={dropdownItem}
                                    >
                                        Another One
                                    </MenuItem>
                                </MenuList>
                            </ClickAwayListener>
                        </Paper>
                    </Grow>
                )}
            </Popper>
        </div>
    )
}

NotificationControlMenu.propTypes = {
    classes: classesProptype.isRequired,
}

export default NotificationControlMenu
