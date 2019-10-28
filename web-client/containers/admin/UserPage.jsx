import React from 'react'
import Helmet from 'react-helmet'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'
import axios from 'axios'
import ReactRouterPropTypes from 'react-router-prop-types'
import SweetAlert from 'react-bootstrap-sweetalert'
import cx from 'classnames'

// Material UI
import { withStyles } from '@material-ui/core/styles'
import People from '@material-ui/icons/People'
import Check from '@material-ui/icons/Check'
import CircularProgress from '@material-ui/core/CircularProgress'
import List from '@material-ui/core/List'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'
import Checkbox from '@material-ui/core/Checkbox'
import FormControlLabel from '@material-ui/core/FormControlLabel'
import Typography from '@material-ui/core/Typography'
import Input from '@material-ui/core/Input'

// Material Dashboard
import GridContainer from 'components/Grid/GridContainer'
import GridItem from 'components/Grid/GridItem'
import Card from '@dashboard/components/Card/Card'
import CardBody from '@dashboard/components/Card/CardBody'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'
import Button from '@dashboard/components/CustomButtons/Button'

import userProfileStyles from '@dashboard/assets/jss/material-dashboard-pro-react/views/userProfileStyles'
import sweetAlertStyle from '@dashboard/assets/jss/material-dashboard-pro-react/views/sweetAlertStyle'
import typographyStyle from '@dashboard/assets/jss/material-dashboard-pro-react/components/typographyStyle'
import customCheckboxRadioSwitchStyle from '@dashboard/assets/jss/material-dashboard-pro-react/customCheckboxRadioSwitch'
import customInputStyle from 'assets/jss/material-dashboard-pro-react/components/customInputStyle'

import { dangerColor, warningColor } from '@dashboard/assets/jss/material-dashboard-pro-react'

import '@dashboard/assets/scss/material-dashboard-pro-react/plugins/_plugin-react-bootstrap-sweetalert.scss'

import { classes as classesPropype } from 'app-proptypes'
import { API_URL } from 'config'
import _ from 'lang'
import { formateCalendar } from 'utils'
import Locale from '../Locale'

const style = (theme) => ({
    ...userProfileStyles,
    ...sweetAlertStyle,
    ...typographyStyle,
    ...customCheckboxRadioSwitchStyle,
    ...customInputStyle,
    dangerBorder: {
        border: `2px solid ${dangerColor[3]}`,
    },
    warningBorder: {
        border: `2px solid ${warningColor[3]}`,
    },
    progress: {
        margin: theme.spacing(2),
    },
    inline: {
        display: 'inline',
    },
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

class UserPage extends React.Component {
    static propTypes = {
        history: ReactRouterPropTypes.history.isRequired,
        match: ReactRouterPropTypes.match.isRequired,
        classes: classesPropype.isRequired,
        langCode: PropTypes.string.isRequired,
        currency: PropTypes.string.isRequired,
    }

    state = {
        alert: null,
        user: null,
        userChanges: {},
        customer: null,
    }

    componentDidMount() {
        this.fetchUser()
    }

    confirmDeletetion = () => {
        const { classes } = this.props

        return (
            <SweetAlert
                warning
                style={{
                    display: 'block',
                    marginTop: '-100px',
                }}
                title={_('Are you sure?', 'admin_user_page')}
                onConfirm={() => this.deleteUser()}
                onCancel={() => this.hideAlert()}
                confirmBtnCssClass={cx(classes.button, classes.success)}
                cancelBtnCssClass={cx(classes.button, classes.danger)}
                confirmBtnText={_('Yes, delete it!', 'admin_user_page')}
                cancelBtnText={_('Cancel')}
                showCancel
            >
                <Locale ns="admin_user_page">
                    This action is irreversible and You won&apos;t
                    be able to recover user&apos;s data.
                </Locale>
            </SweetAlert>
        )
    }

    deleteConfirmed = () => {
        const { classes } = this.props

        return (
            <SweetAlert
                success
                style={{ display: 'block', marginTop: '-100px' }}
                title={_('Deleted!', 'admin_user_page')}
                onConfirm={() => this.backToUserList()}
                onCancel={() => this.backToUserList()}
                confirmBtnCssClass={cx(classes.button, classes.success)}
            >
                <Locale ns="admin_user_page">
                    The user has been successfully deleted.
                </Locale>
            </SweetAlert>
        )
    }

    confirmDissociation = () => {
        const { classes } = this.props

        return (
            <SweetAlert
                warning
                style={{
                    display: 'block',
                    marginTop: '-100px',
                }}
                title={_('Are you sure?', 'admin_user_page')}
                onConfirm={() => this.dissociateCustomer()}
                onCancel={() => this.hideAlert()}
                confirmBtnCssClass={cx(classes.button, classes.success)}
                cancelBtnCssClass={cx(classes.button, classes.danger)}
                confirmBtnText={_('Yes, dissociate it!', 'admin_user_page')}
                cancelBtnText={_('Cancel')}
                showCancel
            >
                <Locale ns="admin_user_page">
                    This action is irreversible and You won&apos;t be able to
                    reassociate the user&apos;s account with its customer account.
                </Locale>
            </SweetAlert>
        )
    }

    dissociationConfirmed = () => {
        const { classes } = this.props

        return (
            <SweetAlert
                success
                style={{ display: 'block', marginTop: '-100px' }}
                title={_('Dissociated!', 'admin_user_page')}
                onConfirm={() => this.hideAlert()}
                onCancel={() => this.hideAlert()}
                confirmBtnCssClass={cx(classes.button, classes.success)}
            >
                <Locale ns="admin_user_page">
                    The user account and its customer account have been successfully dissociated.
                </Locale>
            </SweetAlert>
        )
    }

    notFound = () => {
        const { classes } = this.props

        return (
            <SweetAlert
                danger
                style={{ display: 'block', marginTop: '-100px' }}
                title={_('Not Found!', 'admin_user_page')}
                onConfirm={() => this.backToUserList()}
                onCancel={() => this.backToUserList()}
                confirmBtnCssClass={cx(classes.button, classes.success)}
            >
                <Locale ns="admin_user_page">
                    The user cannot be found.
                </Locale>
            </SweetAlert>
        )
    }

    async deleteUser() {
        const { match } = this.props
        await axios.delete(`${API_URL}/server/users/${match.params.id}`)
        this.setState({ alert: this.deleteConfirmed })
    }

    async dissociateCustomer() {
        const { customer } = this.state
        await axios.post(`${API_URL}/cafet/clients/${customer.id}/dissociate`)
        this.setState({ alert: this.dissociationConfirmed })
    }

    hideAlert() {
        this.setState({ alert: null })
    }

    handleFormChange({ currentTarget }, field) {
        this.setState(({ userChanges }) => ({
            userChanges: {
                [field]: currentTarget.value,
                ...userChanges,
            },
        }))
    }

    async fetchUser() {
        const { match } = this.props

        try {
            const userResponse = await axios.get(`${API_URL}/server/users/${match.params.id}`)
            const user = userResponse.data

            if (!user) return
            this.setState({ user })

            if (!user.customer_id) return
            const customerResponse = await axios.get(`${API_URL}/cafet/clients/${user.customer_id}`)
            const customer = customerResponse.data

            if (customer) this.setState({ customer })
        } catch (err) {
            if (err.response && err.response.status == 404) this.setState({ alert: this.notFound })
        }
    }

    backToUserList() {
        const { history } = this.props
        history.push('/admin/users')
    }

    async createCustomerAccount() {
        const { match } = this.props
        try {
            await axios.post(`${API_URL}/server/users/${match.params.id}/create-customer`)
            await this.fetchUser()
        } catch (err) {
            // fixme
        }
    }

    render() {
        const { classes, langCode, currency } = this.props
        const {
            user, customer, alert, userChanges,
        } = this.state

        if (!user) {
            return (
                <>
                    <CircularProgress className={classes.progress} />
                    {alert ? alert() : null}
                </>
            )
        }

        return (
            <>
                <Helmet>
                    <title>{`${user.pseudo} \xB7 ${_('Users')}`}</title>
                </Helmet>
                {alert ? alert() : null}
                <GridContainer>
                    <GridItem xs={12} md={7} lg={8}>
                        <Card>
                            <CardHeader color="primary" icon>
                                <CardIcon color="primary">
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
                                    {[
                                        {
                                            label: _('Firstname', 'admin_user_page'),
                                            field: 'firstname',
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
                                    ].map(({ label, field }) => (
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
                                                        onChange={(e) => this.handleFormChange(e, field)}
                                                        classes={{
                                                            input: classes.input,
                                                            disabled: classes.disabled,
                                                            underline: classes.underline,
                                                        }}
                                                    />
                                                )}
                                            />
                                        </ListItem>
                                    ))}
                                    {[
                                        {
                                            label: _('ID', 'admin_user_page'),
                                            value: user.id,
                                        },
                                        {
                                            label: _('Member since', 'admin_user_page'),
                                            value: formateCalendar(user.registration).toLocaleString(langCode),
                                        },
                                        {
                                            label: _('Last sign-in at', 'admin_user_page'),
                                            value: formateCalendar(user.last_signin).toLocaleString(langCode),
                                        },
                                        {
                                            label: _('Sign-in count', 'admin_user_page'),
                                            value: user.signin_count,
                                        },
                                    ].map(({ label, value }) => (
                                        <ListItem alignItems="flex-start" key={label}>
                                            <ListItemText primary={label} secondary={value} />
                                        </ListItem>
                                    ))}
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
                                </List>
                                <Button
                                    disabled={Object.keys(userChanges).length === 0}
                                    color="primary"
                                    round
                                >
                                    Save
                                </Button>
                            </CardBody>
                        </Card>
                        <Card>
                            <CardHeader color="rose" icon>
                                <CardIcon color="rose">
                                    <People />
                                </CardIcon>
                                <h4 className={classes.cardIconTitle}>
                                    <Locale ns="admin_user_page">
                                        Customer account
                                    </Locale>
                                </h4>
                            </CardHeader>
                            <CardBody>
                                {customer ? (
                                    <List>
                                        <ListItem alignItems="flex-start">
                                            <ListItemText
                                                primary={_('ID', 'admin_user_page')}
                                                secondary={customer.id}
                                            />
                                        </ListItem>
                                        <ListItem alignItems="flex-start">
                                            <ListItemText
                                                primary={_('Balance', 'admin_user_page')}
                                                secondary={`${customer.balance.toFixed(2)} ${currency}`}
                                            />
                                        </ListItem>
                                        <ListItem alignItems="flex-start">
                                            <ListItemText
                                                primary={_('Member', 'admin_user_page')}
                                                secondary={(customer.member ? _('Yes') : _('No'))}
                                            />
                                        </ListItem>
                                    </List>
                                ) : (
                                    <>
                                        <p>
                                            <Locale ns="admin_user_page" name={user.pseudo}>
                                                create_customer_account_text
                                            </Locale>
                                        </p>
                                        <Button color="rose" onClick={() => this.createCustomerAccount()}>
                                            <Locale ns="admin_user_page">
                                                Create a customer account
                                            </Locale>
                                        </Button>
                                    </>
                                )}
                            </CardBody>
                        </Card>
                    </GridItem>
                    <GridItem xs={12} md={5} lg={4}>
                        <Card className={classes.dangerBorder}>
                            <CardHeader color="danger" icon>
                                <CardIcon color="danger">
                                    <People />
                                </CardIcon>
                                <h4 className={cx(classes.cardIconTitle, classes.dangerText)}>
                                    <Locale ns="admin_user_page">
                                        Delete account
                                    </Locale>
                                </h4>
                            </CardHeader>
                            <CardBody>
                                <p>
                                    Suspendisse semper luctus bibendum. In pellentesque elit ligula, non hendrerit
                                    massa facilisis blandit. Maecenas ultricies mollis elementum. Vestibulum non
                                    venenatis nibh. Ut in nibh non arcu feugiat imperdiet. Phasellus at dui vel
                                    ipsum ultrices sollicitudin. Fusce non pretium ipsum. Sed tristique lobortis
                                    mauris venenatis ornare. Integer id justo metus. Maecenas ornare faucibus turpis
                                    vehicula vulputate. Nunc efficitur ultricies arcu, ut elementum libero viverra eu.
                                    Sed ultricies sollicitudin fermentum.
                                </p>
                                <Button color="danger" onClick={() => this.setState({ alert: this.confirmDeletetion })}>
                                    <Locale>Delete</Locale>
                                </Button>
                            </CardBody>
                        </Card>
                        {customer ? (
                            <Card className={classes.warningBorder}>
                                <CardHeader color="warning" icon>
                                    <CardIcon color="warning">
                                        <People />
                                    </CardIcon>
                                    <h4 className={cx(classes.cardIconTitle, classes.warningText)}>
                                        <Locale ns="admin_user_page">
                                            Dissociate customer account
                                        </Locale>
                                    </h4>
                                </CardHeader>
                                <CardBody>
                                    <p>
                                        Suspendisse semper luctus bibendum. In pellentesque elit ligula, non hendrerit
                                        massa facilisis blandit. Maecenas ultricies mollis elementum. Vestibulum non
                                        venenatis nibh. Ut in nibh non arcu feugiat imperdiet. Phasellus at dui vel
                                        ipsum ultrices sollicitudin. Fusce non pretium ipsum. Sed tristique lobortis
                                        mauris venenatis ornare. Integer id justo metus. Maecenas ornare faucibus turpis
                                        vehicula vulputate. Nunc efficitur ultricies arcu, ut elementum libero viverra eu.
                                        Sed ultricies sollicitudin fermentum.
                                    </p>
                                    <Button color="warning" onClick={() => this.setState({ alert: this.confirmDissociation })}>
                                        <Locale>Dissociate</Locale>
                                    </Button>
                                </CardBody>
                            </Card>
                        ) : null}
                    </GridItem>
                </GridContainer>
            </>
        )
    }
}

const mapStateToProps = (state) => ({
    langCode: state.lang.lang_code,
    currency: state.server.currency,
})

export default withStyles(style)(connect(mapStateToProps)(UserPage))
