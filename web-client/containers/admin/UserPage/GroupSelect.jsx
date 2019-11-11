import React from 'react'
import cx from 'classnames'
import {
    func, number, shape, string,
} from 'prop-types'

import { withStyles } from '@material-ui/core/styles'
import FormControl from '@material-ui/core/FormControl'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'
import MenuItem from '@material-ui/core/MenuItem'
import Select from '@material-ui/core/Select'
import Typography from '@material-ui/core/Typography'
import Input from '@material-ui/core/Input'

import customSelectStyle from '@dashboard/assets/jss/material-dashboard-pro-react/customSelectStyle'

import _ from '../../../lang'
import { GROUPS } from '../../../constants'
import { classes as classesProptype, user as userProptype } from '../../../app-proptypes'


const style = {
    ...customSelectStyle,
    overrideHeight: {
        '& > div + div': {
            maxHeight: 'none !important',
        },
    },
}

const GroupSelect = ({
    user, userChanges, classes, handleChange,
}) => (
    <ListItem alignItems="flex-start">
        <ListItemText
            disableTypography
            primary={(
                <Typography>
                    {_('Group')}
                </Typography>
            )}
            secondary={(
                <FormControl fullWidth className={classes.selectFormControl}>
                    <Select
                        MenuProps={{
                            className: cx(
                                classes.selectMenu,
                                classes.overrideHeight,
                            ),
                        }}
                        classes={{
                            select: classes.select,
                        }}
                        value={userChanges.group !== undefined ? userChanges.group : user.group.id}
                        onChange={({ target }) => handleChange(target.value, 'group')}
                        input={<Input name="group" aria-label="group" />}
                    >
                        {GROUPS.map((group, id) => (
                            <MenuItem
                                classes={{
                                    root: classes.selectMenuItem,
                                    selected: classes.selectMenuItemSelected,
                                }}
                                value={id}
                                key={group}
                            >
                                {_(group)}
                            </MenuItem>
                        ))}
                    </Select>
                </FormControl>
            )}
        />
    </ListItem>
)

GroupSelect.propTypes = {
    classes: classesProptype.isRequired,
    handleChange: func.isRequired,
    user: userProptype.isRequired,
    userChanges: shape({
        firstName: string,
        familyName: string,
        pseudo: string,
        email: string,
        phone: string,
        group: number,
    }).isRequired,
}

export default withStyles(style)(GroupSelect)
