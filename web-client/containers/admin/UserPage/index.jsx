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
import CircularProgress from '@material-ui/core/CircularProgress'
import List from '@material-ui/core/List'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'


// Material Dashboard
import GridContainer from '@dashboard/components/Grid/GridContainer'
import GridItem from '@dashboard/components/Grid/GridItem'
import Card from '@dashboard/components/Card/Card'
import CardBody from '@dashboard/components/Card/CardBody'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'
import Button from '@dashboard/components/CustomButtons/Button'

import sweetAlertStyle from '@dashboard/assets/jss/material-dashboard-pro-react/views/sweetAlertStyle'
import typographyStyle from '@dashboard/assets/jss/material-dashboard-pro-react/components/typographyStyle'

import { dangerColor, warningColor } from '@dashboard/assets/jss/material-dashboard-pro-react'

import '@dashboard/assets/scss/material-dashboard-pro-react/plugins/_plugin-react-bootstrap-sweetalert.scss'

import { classes as classesProptype } from 'app-proptypes'
import { API_URL } from 'config'
import _ from 'lang'
import UserInfoList from './UserInfoList'
import Locale from '../../Locale'

const style = (theme) => ({
    ...sweetAlertStyle,
    ...typographyStyle,
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
})

class UserPage extends React.Component {
    static propTypes = {
        history: ReactRouterPropTypes.history.isRequired,
        match: ReactRouterPropTypes.match.isRequired,
        classes: classesProptype.isRequired,
        langCode: PropTypes.string.isRequired,
        currency: PropTypes.string.isRequired,
    }

    state = {
        alert: null,
        user: null,
        customer: null,
    }

    componentDidMount() {
        this.fetchUser().catch(() => {
            // fixme
        })
    }

    confirmDeletion = () => {
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

        try {
            await axios.post(`${API_URL}/cafet/clients/${customer.id}/dissociate`)
            this.setState({
                alert: this.dissociationConfirmed,
                customer: null,
            })
        } catch (e) {
            // fixme
        }
    }

    async patchUser(userChanges) {
        const { match } = this.props

        try {
            await axios.patch(`${API_URL}/server/users/${match.params.id}`, userChanges)
            this.fetchUser()
        } catch (e) {
            // fixme
        }
    }

    hideAlert() {
        this.setState({ alert: null })
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
            if (err.response && err.response.status === 404) this.setState({ alert: this.notFound })
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
            user, customer, alert,
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
                        <UserInfoList
                            onSave={(userChanges) => this.patchUser(userChanges)}
                            langCode={langCode}
                            user={user}
                        />
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
                                    <Locale ns="admin_user_page">
                                        msg_delete
                                    </Locale>
                                </p>
                                <Button color="danger" onClick={() => this.setState({ alert: this.confirmDeletion })}>
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
                                        <Locale ns="admin_user_page">
                                            msg_dissociate
                                        </Locale>
                                    </p>
                                    <Button
                                        color="warning"
                                        onClick={() => this.setState({ alert: this.confirmDissociation })}
                                    >
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
