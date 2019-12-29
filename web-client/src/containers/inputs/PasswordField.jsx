import React from 'react'
import {
    any, bool, objectOf, string,
} from 'prop-types'
import { Visibility, VisibilityOff } from '@material-ui/icons'
import InputAdornment from '@material-ui/core/InputAdornment'
import IconButton from '@material-ui/core/IconButton'
import CustomInput from '@dashboard/components/CustomInput/CustomInput'


const PasswordField = ({
    id, formControlProps, helperText, error, ...props
}) => {
    const [showPassword, setShowPassword] = React.useState(false)

    return (
        <CustomInput
            id={id}
            error={error}
            helperText={helperText}
            formControlProps={formControlProps}
            inputProps={{
                type: showPassword ? 'text' : 'password',
                autoComplete: 'off',
                endAdornment: (
                    <InputAdornment position="end">
                        <IconButton
                            aria-label="toggle password visibility"
                            onClick={() => setShowPassword(!showPassword)}
                            onMouseDown={(e) => e.preventDefault()}
                        >
                            {showPassword ? <Visibility /> : <VisibilityOff />}
                        </IconButton>
                    </InputAdornment>
                ),
                ...props,
            }}
        />
    )
}

PasswordField.propTypes = {
    id: string,
    error: bool,
    helperText: string,
    formControlProps: objectOf(any),
}

PasswordField.defaultProps = {
    id: null,
    error: false,
    helperText: null,
    formControlProps: { fullWidth: true },
}

export default PasswordField
