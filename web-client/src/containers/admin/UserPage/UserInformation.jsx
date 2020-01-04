import React from 'react'
import {
    func, string,
} from 'prop-types'

import { withStyles } from '@material-ui/core/styles'
import People from '@material-ui/icons/People'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'
import List from '@material-ui/core/List'

import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'
import CardBody from '@dashboard/components/Card/CardBody'
import Button from '@dashboard/components/CustomButtons/Button'
import Card from '@dashboard/components/Card/Card'

import style from '@dashboard/assets/jss/material-dashboard-pro-react/views/userProfileStyles'

import _ from '../../../lang'
import { formatCalendar } from '../../../utils'
import { classes as classesProptype, user as userProptype } from '../../../app-proptypes'
import EditableUserField from './EditableUserField'
import GroupSelect from './GroupSelect'
import MailPreferences from './MailPreferences'
import Locale from '../../Locale'


const editableFields = [
    {
        label: _('First name', 'admin_user_page'),
        field: 'firstName',
    },
    {
        label: _('Family name', 'admin_user_page'),
        field: 'familyName',
    },
    {
        label: _('Username', 'admin_user_page'),
        field: 'pseudo',
    },
    {
        label: _('Email address', 'admin_user_page'),
        field: 'email',
    },
    {
        label: _('Phone number', 'admin_user_page'),
        field: 'phone',
    },
]

const viewOnlyFields = (user, langCode) => ([
    {
        label: _('ID', 'admin_user_page'),
        value: user.id,
    },
    {
        label: _('Member since', 'admin_user_page'),
        value: formatCalendar(user.registration).toLocaleString(langCode),
    },
    {
        label: _('Last sign-in at', 'admin_user_page'),
        value: formatCalendar(user.last_signin).toLocaleString(langCode),
    },
    {
        label: _('Sign-in count', 'admin_user_page'),
        value: user.signin_count,
    },
])

class UserInformation extends React.Component {
    static propTypes = {
        onSave: func.isRequired,
        classes: classesProptype.isRequired,
        user: userProptype.isRequired,
        langCode: string.isRequired,
    }

    state = {
        userChanges: {},
    }

    handleChange(value, field) {
        this.setState(({ userChanges }) => ({
            userChanges: {
                ...userChanges,
                [field]: value,
            },
        }))
    }

    async save() {
        const { onSave } = this.props
        const { userChanges } = this.state

        try {
            await onSave(userChanges)
            this.setState({
                userChanges: {},
            })
        } catch (e) {
            // fixme
        }
    }

    render() {
        const {
            user, classes, langCode,
        } = this.props
        const { userChanges } = this.state

        return (
            <Card>
                <CardHeader color="info" icon>
                    <CardIcon color="info">
                        <People />
                    </CardIcon>
                    <h4 className={classes.cardIconTitle}>
                        <Locale name={user.pseudo} ns="admin_user_page">
                            %(name)s&apos;s information
                        </Locale>
                    </h4>
                </CardHeader>
                <CardBody>
                    <List>
                        {editableFields.map(({ label, field }) => (
                            <EditableUserField
                                label={label}
                                field={field}
                                handleChange={(value) => this.handleChange(value, field)}
                                user={user}
                                key={field}
                            />
                        ))}
                        <GroupSelect
                            handleChange={(value) => this.handleChange(value, 'group')}
                            userChanges={userChanges}
                            user={user}
                        />
                        {viewOnlyFields(user, langCode).map(({ label, value }) => (
                            <ListItem alignItems="flex-start" key={label}>
                                <ListItemText primary={label} secondary={value} />
                            </ListItem>
                        ))}
                        <MailPreferences user={user} />
                    </List>
                    <Button
                        disabled={Object.keys(userChanges).length === 0}
                        onClick={() => this.save()}
                        color="info"
                        round
                    >
                        <Locale>
                            Save
                        </Locale>
                    </Button>
                </CardBody>
            </Card>
        )
    }
}


export default withStyles(style)(UserInformation)
