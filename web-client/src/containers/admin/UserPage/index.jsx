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

import { classesProptype } from '../../../app-proptypes'
import { API_URL } from '../../../config'
import _ from '../../../lang'
import Locale from '../../Locale'
import UserInformation from './UserInformation'
import ClientInformation from './ClientInformation'
import UserPasswordEdition from './UserPasswordEdition'

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
    }

    state = {
        alert: null,
        user: null,
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
        const { user } = this.state

        try {
            await axios.post(`${API_URL}/cafet/clients/${user.customer_id}/dissociate`)
            await this.fetchUser()
            this.setState({
                alert: this.dissociationConfirmed,
            })
        } catch (e) {
            console.error(e)
            // fixme
        }
    }

    async patchUser(userChanges) {
        const { match } = this.props

        await axios.patch(`${API_URL}/server/users/${match.params.id}`, userChanges)
        this.fetchUser()
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
        } catch (err) {
            if (err.response && err.response.status === 404) this.setState({ alert: this.notFound })
            else console.error(err)
        }
    }

    backToUserList() {
        const { history } = this.props
        history.push('/admin/users')
    }

    render() {
        const { classes, langCode } = this.props
        const {
            user, alert,
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
                        <UserInformation
                            onSave={(userChanges) => this.patchUser(userChanges)}
                            langCode={langCode}
                            user={user}
                        />
                        <UserPasswordEdition user={user} />
                        <ClientInformation onUpdate={() => this.fetchUser()} user={user} />
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
                        {user.customer_id ? (
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
})

export default withStyles(style)(connect(mapStateToProps)(UserPage))
