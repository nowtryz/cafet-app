import React from 'react'
import { history as historyProptype } from 'react-router-prop-types'
import { useSelector } from 'react-redux'
import moment from 'moment'
// Material UI
import HistoryIcon from '@material-ui/icons/History'
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
import { useCurrencyFormatter } from '../../hooks/commonHooks'

const useStyle = makeStyles((theme) => ({
    reload: {
        paddingLeft: theme.spacing(14),
    },
}))

const History = ({ history }) => {
    // Hooks
    const customerId = useSelector((state) => state.user.user.customer_id)
    const currencyFormatter = useCurrencyFormatter()
    const classes = useStyle()
    const { data, isLoading, isError } = useHistory(customerId)

    // Component properties
    const pivot = moment().subtract(1, 'day')
    const onCellClick = (event, row) => {
        if (row.type === 'Expense') {
            history.push(`/dashboard/expense/${row.id}`)
        } else {
            history.push(`/dashboard/reload/${row.id}`)
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
            disablePadding: false,
            label: 'Amount',
            numeric: false,
            cellProps: {
                component: 'th',
                scope: 'row',
            },
            render: (operation) => {
                if (operation.type === 'Expense' || operation.total < 0) {
                    return <Danger>- {currencyFormatter(Math.abs(operation.total))}</Danger>
                }
                return (
                    <div className={classes.reload}>
                        <Success>+ {currencyFormatter(operation.total)}</Success>
                    </div>
                )
            },
        },
        {
            id: 'date',
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
                    <HistoryIcon />
                </CardIcon>
            </CardHeader>
            <CardBody>
                <SimpleTable
                    columns={columns}
                    data={data}
                    onCellClick={onCellClick}
                    isLoading={isLoading}
                    dataIdentifier="key"
                    pointer
                    hover
                />
            </CardBody>
        </Card>
    )
}

History.propTypes = {
    history: historyProptype.isRequired,
}

export default History
