import React from 'react'
import axios from 'axios'
import { Link } from 'react-router-dom'
import ReactRouterPropTypes from 'react-router-prop-types'
import moment from 'moment'

// Material UI
import Group from '@material-ui/icons/Group'

// Material Dashboard
import Card from '@dashboard/components/Card/Card'
import CardBody from '@dashboard/components/Card/CardBody'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'

import { API_URL } from '../../config'
import { formatCalendar } from '../../utils'

import { GROUPS } from '../../constants'
import EnhancedTable from '../tables/EnhancedTable'
import Locale from '../Locale'

class Users extends React.Component {
    static propTypes = {
        history: ReactRouterPropTypes.history.isRequired,
    }

    static group(user) {
        return (
            <Locale>
                {user.group}
            </Locale>
        )
    }

    static userLink(user) {
        return (
            <Link to={`/admin/users/${user.id}`} onClick={(e) => e.preventDefault()}>
                {user.pseudo}
            </Link>
        )
    }

    columns = [
        {
            id: 'pseudo',
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
            id: 'registration',
            disablePadding: true,
            label: 'Created',
            render: (user) => moment(formatCalendar(user.registration)).fromNow(),
        },
        {
            id: 'last_signin',
            disablePadding: true,
            label: 'Last activity',
            render: (user) => moment(formatCalendar(user.last_signin)).fromNow(),
        },
        {
            id: 'email', numeric: false, component: false, disablePadding: true, label: 'E-mail',
        },
        {
            id: 'group', numeric: false, component: false, disablePadding: true, label: 'Group', render: Users.group,
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

export default Users
