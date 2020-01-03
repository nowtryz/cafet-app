import React from 'react'
import { history as historyProptype } from 'react-router-prop-types'
import { useSelector } from 'react-redux'
import moment from 'moment'
// Material UI
import Group from '@material-ui/icons/Group'
import { makeStyles } from '@material-ui/core'
// Material Dashboard
import Card from '@dashboard/components/Card/Card'
import CardBody from '@dashboard/components/Card/CardBody'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'
import Danger from '@dashboard/components/Typography/Danger'
import Success from '@dashboard/components/Typography/Success'
import { momentFromCalendar } from '../../utils'
import useHistory from '../../hooks/useHistory'
import SimpleTable from '../tables/SimpleTable'

const useStyle = makeStyles((theme) => ({
    expense: {
        paddingLeft: theme.spacing(4),
    },
}))

const History = ({ history }) => {
    const [customerId, currency] = useSelector((state) => [
        state.user.user.customer_id,
        state.server.currency,
    ])
    const classes = useStyle()
    const { data, isLoading, isError } = useHistory(customerId)
    const pivot = moment().subtract(1, 'day')
    const onCellClick = (event, row) => {
        if (row.type === 'Expense') {
            history.push(`/expense/${row.id}`)
        } else {
            history.push(`/reload/${row.id}`)
        }
    }

    if (!customerId) {
        return <p>Pas d&lsqo;id client</p>
    }

    if (isError) {
        return <div>Une erreur est survenue !</div>
    }

    const columns = [
        {
            id: 'total',
            disablePadding: true,
            label: 'Amount',
            numeric: false,
            cellProps: {
                component: 'th',
                scope: 'row',
                padding: 'none',
            },
            render: (operation) => {
                if (operation.type === 'Expense') {
                    return (
                        <div className={classes.expense}>
                            <Danger>- {operation.total} {currency}</Danger>
                        </div>
                    )
                }
                return <Success>+ {operation.total} {currency}</Success>
            },
        },
        {
            id: 'date',
            disablePadding: true,
            label: 'Date',
            render: (operation) => {
                const cal = momentFromCalendar(operation.date)
                if (cal.isBefore(pivot)) {
                    return cal.calendar()
                }
                return cal.fromNow()
            },
        },
    ]

    return (
        <Card>
            <CardHeader color="rose" icon>
                <CardIcon color="rose">
                    <Group />
                </CardIcon>
            </CardHeader>
            <CardBody>
                <SimpleTable
                    columns={columns}
                    data={data}
                    onCellClick={onCellClick}
                    isLoading={isLoading}
                />
            </CardBody>
        </Card>
    )
}

History.propTypes = {
    history: historyProptype.isRequired,
}

export default History
