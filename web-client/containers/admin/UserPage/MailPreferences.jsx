import React from 'react'
import cx from 'classnames'


import { makeStyles } from '@material-ui/core/styles'
import { Check } from '@material-ui/icons'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'
import List from '@material-ui/core/List'
import FormControlLabel from '@material-ui/core/FormControlLabel'
import Checkbox from '@material-ui/core/Checkbox'

import customCheckboxRadioSwitchStyle
    from '@dashboard/assets/jss/material-dashboard-pro-react/customCheckboxRadioSwitch'

import _ from '../../../lang'
import { userProptype } from '../../../app-proptypes'


const useCheckboxStyle = makeStyles(customCheckboxRadioSwitchStyle)
const useStyle = makeStyles((theme) => ({
    mailPreferences: {
        paddingTop: 0,
        paddingBottom: 0,
    },
    mailPreferencesControl: {
        color: `${theme.palette.text.primary} !important`,
    },
    mailPreferencesList: {
        padding: 0,
    },
}))

const MailPreferences = ({ user }) => {
    const classes = useStyle()
    const checkboxClasses = useCheckboxStyle()
    return (
        <ListItem alignItems="flex-start">
            <ListItemText
                primary={_('Mail preferences', 'admin_user_page')}
                secondaryTypographyProps={{
                    component: 'div',
                }}
                secondary={(
                    <List className={classes.mailPreferencesList}>
                        <ListItem>
                            <FormControlLabel
                                control={(
                                    <Checkbox
                                        disabled
                                        tabIndex={-1}
                                        checked={user.mail_preferences.payment_notice}
                                        checkedIcon={<Check className={checkboxClasses.checkedIcon} />}
                                        icon={<Check className={checkboxClasses.uncheckedIcon} />}
                                        classes={{
                                            checked: checkboxClasses.checked,
                                            root: classes.mailPreferences,
                                        }}
                                    />
                                )}
                                classes={{
                                    label: checkboxClasses.label,
                                    disabled: cx(
                                        checkboxClasses.disabledCheckboxAndRadio,
                                        classes.mailPreferencesControl),
                                }}
                                label={_('Payment notice', 'admin_user_page')}
                            />
                        </ListItem>
                        <ListItem>
                            <FormControlLabel
                                control={(
                                    <Checkbox
                                        disabled
                                        tabIndex={-1}
                                        checked={user.mail_preferences.reload_notice}
                                        checkedIcon={<Check className={checkboxClasses.checkedIcon} />}
                                        icon={<Check className={checkboxClasses.uncheckedIcon} />}
                                        classes={{
                                            checked: checkboxClasses.checked,
                                            root: classes.mailPreferences,
                                        }}
                                    />
                                )}
                                classes={{
                                    label: checkboxClasses.label,
                                    disabled: cx(
                                        checkboxClasses.disabledCheckboxAndRadio,
                                        classes.mailPreferencesControl),
                                }}
                                label={_('Reload notice', 'admin_user_page')}
                            />
                        </ListItem>
                        <ListItem>
                            <FormControlLabel
                                control={(
                                    <Checkbox
                                        disabled
                                        tabIndex={-1}
                                        checked={user.mail_preferences.reload_request}
                                        checkedIcon={<Check className={checkboxClasses.checkedIcon} />}
                                        icon={<Check className={checkboxClasses.uncheckedIcon} />}
                                        classes={{
                                            checked: checkboxClasses.checked,
                                            root: classes.mailPreferences,
                                        }}
                                    />
                                )}
                                classes={{
                                    label: checkboxClasses.label,
                                    disabled: cx(
                                        checkboxClasses.disabledCheckboxAndRadio,
                                        classes.mailPreferencesControl),
                                }}
                                label={_('Reload requests', 'admin_user_page')}
                            />
                        </ListItem>
                    </List>
                )}
            />
        </ListItem>
    )
}

MailPreferences.propTypes = {
    user: userProptype.isRequired,
}

export default MailPreferences
