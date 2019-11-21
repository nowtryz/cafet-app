import React from 'react'
import { connect } from 'react-redux'
import classNames from 'classnames'
import PropTypes from 'prop-types'
import Button from '@dashboard/components/CustomButtons/Button'

// core components
import ClickAwayListener from '@material-ui/core/ClickAwayListener'
import Grow from '@material-ui/core/Grow'
import Paper from '@material-ui/core/Paper'
import Popper from '@material-ui/core/Popper'
import MenuItem from '@material-ui/core/MenuItem'
import MenuList from '@material-ui/core/MenuList'
import ListItemIcon from '@material-ui/core/ListItemIcon'
import ListItemText from '@material-ui/core/ListItemText'

import { changeLocale as changeLocaleAction } from 'actions'

import USFlagIcon from '@dashboard/assets/img/flags/US.png'
import FranceFlagIcon from '@dashboard/assets/img/flags/FR.png'

const LangControlMenu = ({
    classes, children, buttonProps, changeLocale, ...rest
}) => {
    const [open, setOpen] = React.useState(false)
    const [anchorEl, setAnchorEl] = React.useState(null)
    const handleChangeLocale = (locale) => {
        setOpen(false)
        changeLocale(locale)
    }

    return (
        <div className={classes.managerClasses} {...rest}>
            <Button
                buttonRef={setAnchorEl}
                aria-owns={open ? 'menu-list-grow' : undefined}
                aria-haspopup="true"
                onClick={() => setOpen(!open)}
                {...buttonProps}
            >
                {children}
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
                        id="menu-list-grow"
                    >
                        <Paper>
                            <ClickAwayListener onClickAway={() => setOpen(false)}>
                                <MenuList>
                                    <MenuItem className={classes.menuItem} onClick={() => handleChangeLocale('fr-FR')}>
                                        <ListItemIcon className={classes.icon}>
                                            <img src={FranceFlagIcon} alt="FR" />
                                        </ListItemIcon>
                                        <ListItemText
                                            classes={{ primary: classes.primary }}
                                            primary="Français (FR)"
                                        />
                                    </MenuItem>
                                    <MenuItem className={classes.menuItem} onClick={() => handleChangeLocale('en-US')}>
                                        <ListItemIcon className={classes.icon}>
                                            <img src={USFlagIcon} alt="US" />
                                        </ListItemIcon>
                                        <ListItemText classes={{ primary: classes.primary }} primary="English (US)" />
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

LangControlMenu.propTypes = {
    classes: PropTypes.objectOf(PropTypes.string).isRequired,
    children: PropTypes.oneOfType([
        PropTypes.arrayOf(PropTypes.element),
        PropTypes.element,
    ]).isRequired,
    buttonProps: PropTypes.objectOf(PropTypes.any),
    changeLocale: PropTypes.func.isRequired,
}

LangControlMenu.defaultProps = {
    buttonProps: {},
}

export default connect(null, {
    changeLocale: changeLocaleAction,
})(LangControlMenu)
