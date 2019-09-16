import React from 'react'
import axios from 'axios'
import { Link } from 'react-router-dom'
import ReactRouterPropTypes from 'react-router-prop-types'

// Material UI
import Group from '@material-ui/icons/Group'

// Material Dashboard
import Card from '@dashboard/components/Card/Card'
import CardBody from '@dashboard/components/Card/CardBody'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'

import { API_URL } from 'config'
import { formateCalendar } from 'utils'

import EnhancedTable from '../tables/EnhancedTable'

class Users extends React.Component {
    static propTypes = {
        history: ReactRouterPropTypes.history.isRequired,
    }

    static rows = [
        {
            id: 'pseudo',
            numeric: false,
            component: true,
            disablePadding: true,
            label: 'Username',
            render: Users.userLink,
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
            id: 'registration', numeric: false, component: false, disablePadding: true, label: 'Created on',
        },
        {
            id: 'last_signin', numeric: false, component: false, disablePadding: true, label: 'Last activity',
        },
        {
            id: 'email', numeric: false, component: false, disablePadding: true, label: 'E-mail',
        },
        {
            id: 'group', numeric: false, component: false, disablePadding: true, label: 'Group',
        },
    ]

    static groups = [
        'Guest',
        'Consumer',
        'Cafet\' Manager',
        'Cafet\' Administrator',
        'Administrator',
        'Super User',
    ]

    static userLink(user) {
        return (
            <Link to={`/admin/users/${user.id}`} onClick={(e) => e.preventDefault()}>
                {user.pseudo}
            </Link>
        )
    }

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

    async fetchUsers() {
        const response = await axios.get(`${API_URL}/server/users`)

        if (response.data) {
            this.setState({
                users: response.data.map((user) => ({
                    ...user,
                    name: `${user.firstname} ${user.familyName}`,
                    registration: formateCalendar(user.registration).toLocaleDateString(),
                    last_signin: formateCalendar(user.last_signin).toLocaleDateString(),
                    group: Users.groups[user.group.id],
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
                    <EnhancedTable rows={Users.rows} data={users} title="Users" onCellClick={this.onCellClick} />
                </CardBody>
            </Card>
        )
    }
}

export default Users
