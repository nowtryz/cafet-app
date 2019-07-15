import React from 'react'
import axios from 'axios'
import ReactRouterPropTypes from 'react-router-prop-types'
import SweetAlert from 'react-bootstrap-sweetalert'
import cx from 'classnames'

//Material UI
import { withStyles } from '@material-ui/core/styles'
import People from '@material-ui/icons/People'

// Material Dashboard
import GridContainer from 'components/Grid/GridContainer'
import GridItem from 'components/Grid/GridItem'
import Card from '@dashboard/components/Card/Card'
import CardBody from '@dashboard/components/Card/CardBody'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'
import Button from '@dashboard/components/CustomButtons/Button'
// eslint-disable-next-line no-unused-vars
import Danger from 'components/Typography/Danger'

import userProfileStyles from '@dashboard/assets/jss/material-dashboard-pro-react/views/userProfileStyles'
import sweetAlertStyle from '@dashboard/assets/jss/material-dashboard-pro-react/views/sweetAlertStyle'
import typographyStyle from '@dashboard/assets/jss/material-dashboard-pro-react/components/typographyStyle'

import '@dashboard/assets/scss/material-dashboard-pro-react/plugins/_plugin-react-bootstrap-sweetalert.scss'

import {classes as classesPropype} from 'app-proptypes'
import { API_URL } from 'config'
import _ from 'lang'
import Locale from '../Locale'

class UserPage extends React.Component {
    state = {
        alert: null,
        user: {}
    }
    componentDidMount() {
        this.fetchUser()
    }

    confirmDelete = () => {
        const {classes} = this.props

        return (
            <SweetAlert
                warning
                style={{
                    display: 'block',
                    marginTop: '-100px' }}
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
                    You won&apos;t be able to recover user&apos;s data except its balance.
                </Locale>
            </SweetAlert>
        )
    }

    deleteConfirmed = () => {
        const {classes} = this.props

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

    notFound = () => {
        const {classes} = this.props

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
        this.setState({alert: this.deleteConfirmed})
    }

    hideAlert() {
        this.setState({alert: null})
    }

    async fetchUser() {
        const { match } = this.props

        try {
            const response = await axios.get(`${API_URL}/server/users/${match.params.id}`)
    
            if (response.data) {
                this.setState({
                    user: response.data
                })
            }
        } catch(err) {
            if (err.response && err.response.status == 404) this.setState({alert: this.notFound})
        }
    }

    backToUserList() {
        const {history} = this.props
        history.push('/admin/users')
    }

    render() {
        const {classes} = this.props
        const {user, alert} = this.state

        return (
            <React.Fragment>
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
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin consequat
                                auctor leo id tempus. In hac habitasse platea dictumst. Vestibulum a tortor
                                libero. In id ipsum at velit dapibus lacinia. Praesent diam felis, pharetra
                                et maximus sit amet, porta ac magna. Phasellus fermentum, nibh et lobortis
                                bibendum, nibh libero rhoncus eros, nec porta risus nunc sit amet lacus.
                                Phasellus hendrerit vehicula auctor. Vestibulum malesuada sem quis lacinia
                                finibus. Vivamus pretium in lorem quis feugiat. Mauris sit amet consequat
                                nisl, non lacinia sem.
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
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin consequat
                                auctor leo id tempus. In hac habitasse platea dictumst. Vestibulum a tortor
                                libero. In id ipsum at velit dapibus lacinia. Praesent diam felis, pharetra
                                et maximus sit amet, porta ac magna. Phasellus fermentum, nibh et lobortis
                                bibendum, nibh libero rhoncus eros, nec porta risus nunc sit amet lacus.
                                Phasellus hendrerit vehicula auctor. Vestibulum malesuada sem quis lacinia
                                finibus. Vivamus pretium in lorem quis feugiat. Mauris sit amet consequat
                                nisl, non lacinia sem.
                            </CardBody>
                        </Card>
                    </GridItem>
                    <GridItem xs={12} md={5} lg={4}>
                        <Card>
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
                                <p className={classes.dangerText}>
                                    Suspendisse semper luctus bibendum. In pellentesque elit ligula, non hendrerit
                                    massa facilisis blandit. Maecenas ultricies mollis elementum. Vestibulum non
                                    venenatis nibh. Ut in nibh non arcu feugiat imperdiet. Phasellus at dui vel
                                    ipsum ultrices sollicitudin. Fusce non pretium ipsum. Sed tristique lobortis
                                    mauris venenatis ornare. Integer id justo metus. Maecenas ornare faucibus turpis
                                    vehicula vulputate. Nunc efficitur ultricies arcu, ut elementum libero viverra eu.
                                    Sed ultricies sollicitudin fermentum.
                                </p>
                                <Button color="danger" onClick={() => this.setState({alert: this.confirmDelete})}>
                                    <Locale>Delete</Locale>
                                </Button>
                            </CardBody>
                        </Card>
                    </GridItem>
                </GridContainer>
            </React.Fragment>
        )
    }
}

UserPage.propTypes = {
    history: ReactRouterPropTypes.history.isRequired,
    match: ReactRouterPropTypes.match.isRequired,
    classes: classesPropype.isRequired,
}

export default withStyles({
    ...userProfileStyles,
    ...sweetAlertStyle,
    ...typographyStyle
})(UserPage)