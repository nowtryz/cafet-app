import React from 'react'
import classNames from 'classnames'
import PropTypes from 'prop-types'

// Material UI
import { withStyles } from '@material-ui/core/styles'
import Toolbar from '@material-ui/core/Toolbar'
import Tooltip from '@material-ui/core/Tooltip'
import Typography from '@material-ui/core/Typography'
import IconButton from '@material-ui/core/IconButton'
import DeleteIcon from '@material-ui/icons/Delete'
import FilterListIcon from '@material-ui/icons/FilterList'
import { lighten } from '@material-ui/core/styles/colorManipulator'

import { classes as classesProptype } from 'app-proptypes'
import _ from 'lang'
import Locale from '../Locale'

const toolbarStyles = theme => ({
    root: {
        paddingRight: theme.spacing.unit,
    },
    highlight:
    theme.palette.type === 'light'
        ? {
            color: theme.palette.secondary.main,
            backgroundColor: lighten(theme.palette.secondary.light, 0.85),
        }
        : {
            color: theme.palette.text.primary,
            backgroundColor: theme.palette.secondary.dark,
        },
    spacer: {
        flex: '1 1 100%',
    },
    actions: {
        color: theme.palette.text.secondary,
    },
    title: {
        flex: '0 0 auto',
    },
})

const EnhancedTableToolbar = ({ numSelected, classes, title }) => {

    return (
        <Toolbar
            className={classNames(classes.root, {
                [classes.highlight]: numSelected > 0,
            })}
        >
            <div className={classes.title}>
                {numSelected > 0 ? (
                    <Typography color="inherit" variant="subtitle1">
                        <Locale variables={[numSelected]}>%d selected</Locale>
                    </Typography>
                ) : (
                    <Typography variant="h6" id="tableTitle">
                        <Locale>
                            {title}
                        </Locale>
                    </Typography>
                )}
            </div>
            <div className={classes.spacer} />
            <div className={classes.actions}>
                {numSelected > 0 ? (
                    <Tooltip title={_('Delete')}>
                        <IconButton aria-label={_('Delete')}>
                            <DeleteIcon />
                        </IconButton>
                    </Tooltip>
                ) : (
                    <Tooltip title={_('Filter')}>
                        <IconButton aria-label={_('Filter')}>
                            <FilterListIcon />
                        </IconButton>
                    </Tooltip>
                )}
            </div>
        </Toolbar>
    )
}

EnhancedTableToolbar.propTypes = {
    classes: classesProptype.isRequired,
    numSelected: PropTypes.number.isRequired,
    title: PropTypes.string.isRequired
}

export default withStyles(toolbarStyles)(EnhancedTableToolbar)