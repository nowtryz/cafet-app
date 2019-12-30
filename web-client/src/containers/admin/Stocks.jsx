import React from 'react'
import axios from 'axios'
import { history as historyProptype } from 'react-router-prop-types'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'

// Material UI
import Group from '@material-ui/icons/Group'

// Material Dashboard
import Card from '@dashboard/components/Card/Card'
import CardBody from '@dashboard/components/Card/CardBody'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'

import { API_URL } from 'config'
import { formateCalendar } from 'utils'

import { GROUPS } from '../../constants'
import EnhancedTable from '../tables/EnhancedTable'

class Stocks extends React.Component {
    static propTypes = {
        history: historyProptype.isRequired,
        langCode: PropTypes.string.isRequired,
    }

    columns = [
        {
            id: 'pseudo',
            disablePadding: true,
            label: 'Username',
            render: Stocks.userLink,
            cellProps: {
                component: 'th',
                scope: 'row',
                padding: 'none',
            },
        },
        {
            id: 'name', numeric: false, component: false, disablePadding: true, label: 'Name',
        },
        {
            id: 'registration',
            disablePadding: true,
            label: 'Created on',
            render: (user) => this.date(user, 'registration'),
        },
        {
            id: 'last_signin',
            disablePadding: true,
            label: 'Last activity',
            render: (user) => this.date(user, 'last_signin'),
        },
        {
            id: 'email', numeric: false, component: false, disablePadding: true, label: 'E-mail',
        },
        {
            id: 'group', numeric: false, component: false, disablePadding: true, label: 'Group', render: Stocks.group,
        },
    ]

    state = {
        users: [],
    }

    componentDidMount() {
        this.fetchUsers()
    }

    onCellClick = (event, id) => {
        const { history } = this.props
        history.push(`/admin/users/${id}`)
    }

    date(user, field) {
        const { langCode } = this.props
        return formateCalendar(user[field]).toLocaleDateString(langCode)
    }

    async fetchUsers() {
        const response = await axios.get(`${API_URL}/server/users`)

        if (response.data) {
            this.setState({
                users: response.data.map((user) => ({
                    ...user,
                    name: `${user.firstName} ${user.familyName}`,
                    group: GROUPS[user.group.id],
                })),
            })
        }
    }

    render() {
        const { users } = this.state

        return (
            <Card>
                <CardHeader color="rose" icon>
                    <CardIcon color="rose">
                        <Group />
                    </CardIcon>
                </CardHeader>
                <CardBody>
                    <EnhancedTable columns={this.columns} data={users} title="Users" onCellClick={this.onCellClick} />
                </CardBody>
            </Card>
        )
    }
}

const mapStateToProps = (state) => ({
    langCode: state.lang.lang_code,
})

export default connect(mapStateToProps)(Stocks)
