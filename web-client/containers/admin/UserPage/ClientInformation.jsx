import React from 'react'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'
import axios from 'axios'

import { withStyles } from '@material-ui/core/styles'
import List from '@material-ui/core/List'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'

import People from '@material-ui/icons/People'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'
import CardBody from '@dashboard/components/Card/CardBody'
import Button from '@dashboard/components/CustomButtons/Button'
import Card from '@dashboard/components/Card/Card'

import style from '@dashboard/assets/jss/material-dashboard-pro-react/views/userProfileStyles'

import Locale from '../../Locale'
import _ from '../../../lang'
import { classes as classesProptype, user as userProptype } from '../../../app-proptypes'
import { API_URL } from '../../../config'

class ClientInformation extends React.Component {
    static propTypes = {
        onUpdate: PropTypes.func.isRequired,
        classes: classesProptype.isRequired,
        user: userProptype.isRequired,
        currency: PropTypes.string.isRequired,
    }

    state = {
        customer: null,
    }

    componentDidMount() {
        const { user } = this.props
        this.fetchCustomer(user.customer_id).catch(() => {
            // fixme
        })
    }

    componentDidUpdate(prevProps) {
        const { user } = this.props

        if (prevProps.user.customer_id !== user.customer_id) {
            this.fetchCustomer(user.customer_id).catch(() => {
                // fixme
            })
        }
    }

    async createCustomerAccount() {
        const { user, onUpdate } = this.props

        try {
            await axios.post(`${API_URL}/server/users/${user.id}/create-customer`)
            await onUpdate()
        } catch (err) {
            console.error(err)
            // fixme
        }
    }

    async fetchCustomer(customerId) {
        if (!customerId) {
            this.setState({
                customer: null,
            })
            return
        }

        try {
            const customerResponse = await axios.get(`${API_URL}/cafet/clients/${customerId}`)
            const customer = customerResponse.data

            if (customer) this.setState({ customer })
        } catch (err) {
            // fixme
        }
    }

    render() {
        const { classes, user, currency } = this.props
        const { customer } = this.state

        return (
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
        )
    }
}

const mapStateToProps = (state) => ({
    currency: state.server.currency,
})


export default withStyles(style)(connect(mapStateToProps)(ClientInformation))
