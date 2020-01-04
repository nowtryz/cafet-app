import React from 'react'
import ReactRouterPropTypes from 'react-router-prop-types'
import { makeStyles, Typography } from '@material-ui/core'
import Grid from '@material-ui/core/Grid'
import Badge from '@material-ui/core/Badge'
import Hidden from '@material-ui/core/Hidden'
import ListItemIcon from '@material-ui/core/ListItemIcon'
import ListItemText from '@material-ui/core/ListItemText'
import ListItem from '@material-ui/core/ListItem'
import List from '@material-ui/core/List'
import TableRow from '@material-ui/core/TableRow'
import TableCell from '@material-ui/core/TableCell'
import Table from '@material-ui/core/Table'
import ShoppingCart from '@material-ui/icons/ShoppingCart'
import Image from '@material-ui/icons/Image'
import Schedule from '@material-ui/icons/Schedule'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'
import CardBody from '@dashboard/components/Card/CardBody'
import Card from '@dashboard/components/Card/Card'
import userProfileStyles from '@dashboard/assets/jss/material-dashboard-pro-react/views/userProfileStyles'
import SimpleTable from '../tables/SimpleTable'
import { useCurrencyFormatter } from '../../hooks/commonHooks'
import useExpense from '../../hooks/useExpense'
import Locale from '../Locale'
import { momentFromCalendar } from '../../utils'
import _ from '../../lang'

const IMG_SIZE = 40
const useProfileStyle = makeStyles(userProfileStyles)
const useStyle = makeStyles((theme) => ({
    image: {
        maxHeight: IMG_SIZE,
        maxWidth: IMG_SIZE,
    },
    imageCell: {
        width: 50,
    },
    badge: {
        top: 5,
        // The border color match the background color.
        border: `2px solid ${theme.palette.background.paper}`,
    },
    info: {
        textAlign: 'right',
    },
    value: {
        textAlign: 'center',
    },
    infoTable: {
        width: 'fit-content',
        marginTop: theme.spacing(3),
    },
}))

const Expense = ({ match }) => {
    const {
        expense, details, isError, isLoading, error,
    } = useExpense(match.params.id)
    const profileClasses = useProfileStyle()
    const { badge, ...classes } = useStyle()
    const currencyFormat = useCurrencyFormatter()

    if (isError) {
        return (
            <div>
                An error occurred
                {error ? `: ${error}` : '.'}
            </div>
        )
    }

    const columns = [
        {
            id: 'image',
            disablePadding: true,
            label: '',
            render: (row) => (
                <Grid container justify="center" alignItems="center">
                    <Hidden smUp>
                        <Badge classes={{ badge }} badgeContent={row.quantity} color="secondary">
                            {row.img ? <img className={classes.image} src={row.img} alt="Product" /> : <Image />}
                        </Badge>
                    </Hidden>
                    <Hidden xsDown>
                        {row.img ? <img className={classes.image} src={row.img} alt="Product" /> : <Image />}
                    </Hidden>
                </Grid>
            ),
            cellProps: {
                className: classes.imageCell,
            },
        },
        {
            id: 'name',
            label: 'Designation',
            cellProps: {
                component: 'th',
                scope: 'row',
            },
        },
        {
            id: 'price',
            label: 'Price',
            numeric: true,
            render: (row) => currencyFormat(row.price),
        },
        {
            id: 'quantity',
            label: 'Quantity',
            numeric: true,
            hidden: 'xs',
        },
    ]

    return (
        <>
            <Card>
                <CardHeader color="rose" icon>
                    <CardIcon color="rose">
                        <ShoppingCart />
                    </CardIcon>
                    <h4 className={profileClasses.cardIconTitle}>
                        <Locale ns="expense_page">
                            Details of your order
                        </Locale>
                    </h4>
                </CardHeader>
                <CardBody>
                    <List>
                        <ListItem>
                            <ListItemIcon>
                                <Schedule />
                            </ListItemIcon>
                            <ListItemText primary={isLoading
                                ? `${_('Loading')}...`
                                : momentFromCalendar(expense.date).format('LLLL')}
                            />
                        </ListItem>
                    </List>
                    <SimpleTable
                        columns={columns}
                        data={details}
                        isLoading={isLoading}
                        dataIdentifier="id"
                        ns="expense_page"
                        noMargin
                    />
                    {isLoading ? null : (
                        <Grid container justify="flex-end">
                            <Table className={classes.infoTable}>
                                {[
                                    ['Item count', details.map((detail) => detail.quantity).reduce((a, b) => a + b)],
                                    ['Total order', currencyFormat(expense.total)],
                                ].map(([info, value]) => (
                                    <TableRow key={info}>
                                        <TableCell className={classes.info}>
                                            <Typography>
                                                <Locale ns="expense_page">
                                                    {info}
                                                </Locale>
                                            </Typography>
                                        </TableCell>
                                        <TableCell className={classes.value}>
                                            <Typography variant="body2">
                                                {value}
                                            </Typography>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </Table>
                        </Grid>
                    )}
                </CardBody>
            </Card>
        </>
    )
}

Expense.propTypes = {
    match: ReactRouterPropTypes.match.isRequired,
}

export default Expense
