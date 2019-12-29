import React from 'react'
import { func, string } from 'prop-types'

import { makeStyles } from '@material-ui/core/styles'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'
import Typography from '@material-ui/core/Typography'
import Input from '@material-ui/core/Input'

import style from '@dashboard/assets/jss/material-dashboard-pro-react/components/customInputStyle'

import { user as userProptype } from '../../../app-proptypes'


const useStyle = makeStyles(style)

const EditableUserField = ({
    label, field, handleChange, user,
}) => {
    const classes = useStyle()
    return (
        <ListItem alignItems="flex-start" key={field}>
            <ListItemText
                disableTypography
                primary={<Typography>{label}</Typography>}
                secondary={(
                    <Input
                        inputProps={{
                            defaultValue: user[field],
                            'aria-label': label,
                        }}
                        onChange={({ currentTarget }) => handleChange(currentTarget.value, field)}
                        classes={{
                            input: classes.input,
                            disabled: classes.disabled,
                            underline: classes.underline,
                        }}
                    />
                )}
            />
        </ListItem>
    )
}

EditableUserField.propTypes = {
    label: string.isRequired,
    field: string.isRequired,
    handleChange: func.isRequired,
    user: userProptype.isRequired,
}

export default EditableUserField
