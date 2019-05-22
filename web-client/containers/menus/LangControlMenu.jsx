import React from 'react'
import { connect } from 'react-redux'
import classNames from 'classnames'
import PropTypes from 'prop-types'
import Button from '@dashboard/components/CustomButtons/Button'
import { withStyles } from '@material-ui/core/styles'

// core components
import ClickAwayListener from '@material-ui/core/ClickAwayListener'
import Grow from '@material-ui/core/Grow'
import Paper from '@material-ui/core/Paper'
import Popper from '@material-ui/core/Popper'
import MenuItem from '@material-ui/core/MenuItem'
import MenuList from '@material-ui/core/MenuList'
import ListItemIcon from '@material-ui/core/ListItemIcon'
import ListItemText from '@material-ui/core/ListItemText'

import adminNavbarLinksStyle from '@dashboard/assets/jss/material-dashboard-pro-react/components/adminNavbarLinksStyle'

import { changeLocale as changeLocaleAction } from 'actions'
import USFlagIcon from '../flags/USFlagIcon'
import FranceFlagIcon from '../flags/FranceFlagIcon'

const styles = theme => ({
    ...adminNavbarLinksStyle(theme),
    menuItem: {
        paddingRight: '30px',
        paddingLeft: '30px',
    }
})

class LangControlMenu extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        children: PropTypes.oneOfType([
            PropTypes.arrayOf(PropTypes.element),
            PropTypes.element
        ]).isRequired,
        buttonProps: PropTypes.objectOf(PropTypes.any),
        changeLocale: PropTypes.func.isRequired
    }

    static defaultProps = {
        buttonProps: {}
    }

    state = {
        open: false,
    }

    handleToggle = () => {
        this.setState(state => ({ open: !state.open }))
    }

    handleClose = event => {
        if (this.anchorEl.contains(event.target)) {
            return
        }

        this.setState({ open: false })
    }

    changeLocale(locale) {
        const { changeLocale } = this.props
        this.setState({ open: false })
        changeLocale(locale)
    }

    render() {
        const { classes, children, logout, buttonProps, changeLocale, ...rest } = this.props
        const { open } = this.state

        return (
            <div {...rest}>
                <Button
                    buttonRef={node => {
                        this.anchorEl = node
                    }}
                    aria-owns={open ? 'menu-list-grow' : undefined}
                    aria-haspopup='true'
                    onClick={this.handleToggle}
                    {...buttonProps}
                >
                    {children}
                </Button>
                <Popper
                    open={open}
                    anchorEl={this.anchorEl}
                    transition
                    disablePortal
                    placement='bottom'
                    className={classNames({
                        [classes.popperClose]: !open,
                        [classes.pooperResponsive]: true,
                        [classes.pooperNav]: true
                    })}
                >
                    {({ TransitionProps }) => (
                        <Grow
                            {...TransitionProps}
                            id='menu-list-grow'
                        >
                            <Paper>
                                <ClickAwayListener onClickAway={this.handleClose}>
                                    <MenuList>
                                        <MenuItem className={classes.menuItem} onClick={() => this.changeLocale('fr_FR')}>
                                            <ListItemIcon className={classes.icon}>
                                                <FranceFlagIcon />
                                            </ListItemIcon>
                                            <ListItemText classes={{ primary: classes.primary }} inset primary="Français (FR)" />
                                        </MenuItem>
                                        <MenuItem className={classes.menuItem} onClick={() => this.changeLocale('en_US')}>
                                            <ListItemIcon className={classes.icon}>
                                                <USFlagIcon />
                                            </ListItemIcon>
                                            <ListItemText classes={{ primary: classes.primary }} inset primary="Englis (US)" />
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
}

export default withStyles(styles)(connect(null, {
    changeLocale: changeLocaleAction
})(LangControlMenu))