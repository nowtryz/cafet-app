import React from 'react'
import cx from 'classnames'


import { withStyles } from '@material-ui/core/styles'
import { Check } from '@material-ui/icons'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'
import List from '@material-ui/core/List'
import FormControlLabel from '@material-ui/core/FormControlLabel'
import Checkbox from '@material-ui/core/Checkbox'

import customCheckboxRadioSwitchStyle
    from '@dashboard/assets/jss/material-dashboard-pro-react/customCheckboxRadioSwitch'

import _ from '../../../lang'
import { classesProptype, userProptype } from '../../../app-proptypes'


const style = (theme) => ({
    ...customCheckboxRadioSwitchStyle,
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
})

const MailPreferences = ({ classes, user }) => (
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
                                    checkedIcon={<Check className={classes.checkedIcon} />}
                                    icon={<Check className={classes.uncheckedIcon} />}
                                    classes={{
                                        checked: classes.checked,
                                        root: classes.mailPreferences,
                                    }}
                                />
                            )}
                            classes={{
                                label: classes.label,
                                disabled: cx(classes.disabledCheckboxAndRadio, classes.mailPreferencesControl),
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
                                    checkedIcon={<Check className={classes.checkedIcon} />}
                                    icon={<Check className={classes.uncheckedIcon} />}
                                    classes={{
                                        checked: classes.checked,
                                        root: classes.mailPreferences,
                                    }}
                                />
                            )}
                            classes={{
                                label: classes.label,
                                disabled: cx(classes.disabledCheckboxAndRadio, classes.mailPreferencesControl),
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
                                    checkedIcon={<Check className={classes.checkedIcon} />}
                                    icon={<Check className={classes.uncheckedIcon} />}
                                    classes={{
                                        checked: classes.checked,
                                        root: classes.mailPreferences,
                                    }}
                                />
                            )}
                            classes={{
                                label: classes.label,
                                disabled: cx(classes.disabledCheckboxAndRadio, classes.mailPreferencesControl),
                            }}
                            label={_('Reload requests', 'admin_user_page')}
                        />
                    </ListItem>
                </List>
            )}
        />
    </ListItem>
)

MailPreferences.propTypes = {
    classes: classesProptype.isRequired,
    user: userProptype.isRequired,
}

export default withStyles(style)(MailPreferences)
