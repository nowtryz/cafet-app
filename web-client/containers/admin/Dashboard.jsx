import React from 'react'
import PropTypes from 'prop-types'
// react plugin for creating charts
import ChartistGraph from 'react-chartist'
// react plugin for creating vector maps
import { NavLink } from 'react-router-dom'
import { connect } from 'react-redux'
import axios from 'axios'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'
import Tooltip from '@material-ui/core/Tooltip'
import Icon from '@material-ui/core/Icon'

// @material-ui/icons
// import ContentCopy from '@material-ui/icons/ContentCopy'
import Store from '@material-ui/icons/Store'
// import InfoOutline from '@material-ui/icons/InfoOutline'
import DateRange from '@material-ui/icons/DateRange'
import Group from '@material-ui/icons/Group'
import ArrowUpward from '@material-ui/icons/ArrowUpward'
import ArrowDownward from '@material-ui/icons/ArrowDownward'
import AccessTime from '@material-ui/icons/AccessTime'
import Refresh from '@material-ui/icons/Refresh'
import Edit from '@material-ui/icons/Edit'
import Person from '@material-ui/icons/Person'
import ShoppingCart from '@material-ui/icons/ShoppingCart'
import Timeline from '@material-ui/icons/Timeline'

// core components
import GridContainer from '@dashboard/components/Grid/GridContainer'
import GridItem from '@dashboard/components/Grid/GridItem'
import Button from '@dashboard/components/CustomButtons/Button'
import Card from '@dashboard/components/Card/Card'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'
import CardBody from '@dashboard/components/Card/CardBody'
import CardFooter from '@dashboard/components/Card/CardFooter'

import {
    dangerColor,
    successColor
} from '@dashboard/assets/jss/material-dashboard-pro-react'
import {
    straightLinesChart,
    simpleBarChart,
    completedTasksChart
} from '@dashboard/variables/charts'

import dashboardStyle from '@dashboard/assets/jss/material-dashboard-pro-react/views/dashboardStyle'

import { classes as classesPropType } from 'app-proptypes'
import _ from 'lang'
import Locale from 'containers/Locale'
import { API_URL } from 'config'
import { rotate } from 'utils'

class Dashboard extends React.Component {
    static propTypes = {
        classes: classesPropType.isRequired,
        currency: PropTypes.string.isRequired
    }

    state = {
        total_storage: 0,
        used_storage: 0,
        weekly_revenue: 0,
        montly_sales: 0,
        user_count: 0,
        weekly_balance_reloads: [0,0,0,0,0,0,0],
        last_monthly_sales_count: [0,0,0,0,0,0,0,0,0,0,0,0],
        monthly_subscription: [0,0,0,0,0,0,0,0,0,0,0,0]
    }

    componentDidMount() {
        this.loadStats()
    }

    refresh = () => {
        this.loadStats()
    }

    async loadStats() {
        const response = await axios.get(`${API_URL}/stats/overview`)
        this.setState({...response.data})
    }

    renderWeeklyBalanceReloads() {
        const { classes } = this.props
        const { weekly_balance_reloads } = this.state
        const variation = (weekly_balance_reloads[6]/weekly_balance_reloads[5] - 1) * 100 | 0
        const min = Math.min(...weekly_balance_reloads)
        const data = {
            labels: rotate(_('daysOfWeek').split(''), new Date().getDay()),
            series: [weekly_balance_reloads]
        }
        const options = {
            ...straightLinesChart.options,
            high: Math.max(...weekly_balance_reloads.map(v => (v - min) * 4/3 + min), 50),
            low: min
        }

        return (
            <Card chart className={classes.cardHover}>
                <CardHeader color='info' className={classes.cardHeaderHover}>
                    <ChartistGraph
                        className='ct-chart-white-colors'
                        data={data}
                        type='Line'
                        options={options}
                        listener={straightLinesChart.animation}
                    />
                </CardHeader>
                <CardBody>
                    <div className={classes.cardHoverUnder}>
                        <Tooltip
                            id='tooltip-top'
                            title={_('Refresh')}
                            placement='bottom'
                            classes={{ tooltip: classes.tooltip }}
                        >
                            <Button simple color='info' justIcon onClick={this.refresh}>
                                <Refresh className={classes.underChartIcons} />
                            </Button>
                        </Tooltip>
                    </div>
                    <h4 className={classes.cardTitle}>
                        <Locale>Daily Balance Reloads</Locale>
                    </h4>
                    <p className={classes.cardCategory}>
                        {variation >= 0 ? (
                            <React.Fragment>
                                <span style={{color: successColor[0]}}>
                                    <ArrowUpward className={classes.upArrowCardCategory} /> {variation}%
                                </span>
                                {' '}
                                <Locale>increase in today reloads.</Locale>
                            </React.Fragment>
                        ):(
                            <React.Fragment>
                                <span style={{color: dangerColor[0]}}>
                                    <ArrowDownward className={classes.upArrowCardCategory} /> {-variation}%
                                </span>
                                {' '}
                                <Locale>decrease in today reloads.</Locale>
                            </React.Fragment>
                        )}
                    </p>
                </CardBody>
                <CardFooter chart>
                    <div className={classes.stats}>
                        <DateRange />
                        <Locale>Last 7 Days</Locale>
                    </div>
                </CardFooter>
            </Card>
        )
    }

    renderLastMonthlySalesCount() {
        const { classes } = this.props
        const { last_monthly_sales_count } = this.state
        const variation = (last_monthly_sales_count[11]/last_monthly_sales_count[10] - 1) * 100 | 0
        const min = Math.min(...last_monthly_sales_count)
        const data = {
            labels: rotate(_('monthsOfYear').split(' '), new Date().getMonth() + 1),
            series: [last_monthly_sales_count]
        }
        const options = {
            ...simpleBarChart.options,
            high: Math.max(...last_monthly_sales_count.map(v => (v - min) * 4/3 + min), 50),
            low: min
        }

        return (
            <Card chart className={classes.cardHover}>
                <CardHeader color='warning' className={classes.cardHeaderHover}>
                    <ChartistGraph
                        className='ct-chart-white-colors'
                        data={data}
                        type='Bar'
                        options={options}
                        responsiveOptions={simpleBarChart.responsiveOptions}
                        listener={simpleBarChart.animation}
                    />
                </CardHeader>
                <CardBody>
                    <div className={classes.cardHoverUnder}>
                        <Tooltip
                            id='tooltip-top'
                            title={_('Refresh')}
                            placement='bottom'
                            classes={{ tooltip: classes.tooltip }}
                        >
                            <Button simple color='info' justIcon onClick={this.refresh}>
                                <Refresh className={classes.underChartIcons} />
                            </Button>
                        </Tooltip>
                    </div>
                    <h4 className={classes.cardTitle}>
                        <Locale>Monthly Sales</Locale>
                    </h4>
                    <p className={classes.cardCategory}>
                        {variation >= 0 ? (
                            <React.Fragment>
                                <span style={{color: successColor[0]}}>
                                    <ArrowUpward className={classes.upArrowCardCategory} /> {variation}%
                                </span>
                                {' '}
                                <Locale>of increase this month.</Locale>
                            </React.Fragment>
                        ):(
                            <React.Fragment>
                                <span style={{color: dangerColor[0]}}>
                                    <ArrowDownward className={classes.upArrowCardCategory} /> {-variation}%
                                </span>
                                {' '}
                                <Locale>of decrease this month.</Locale>
                            </React.Fragment>
                        )}
                    </p>
                </CardBody>
                <CardFooter chart>
                    <div className={classes.stats}>
                        <DateRange />
                        <Locale>Last 12 months</Locale>
                    </div>
                </CardFooter>
            </Card>
        )
    }

    render() {
        const { classes, currency } = this.props
        const {
            total_storage,
            used_storage,
            weekly_revenue,
            montly_sales,
            user_count,
            monthly_subscription
        } = this.state

        return (
            <div>
                <GridContainer>
                    <GridItem xs={12} sm={6} md={6} lg={3}>
                        <Card>
                            <CardHeader color='warning' stats icon>
                                <CardIcon color='warning'>
                                    <Icon>content_copy</Icon>
                                </CardIcon>
                                <p className={classes.cardCategory}>
                                    <Locale>Used Space</Locale>
                                </p>
                                <h3 className={classes.cardTitle}>
                                    {used_storage}/{total_storage} <small>GB</small>
                                </h3>
                            </CardHeader>
                            <CardFooter stats>
                                <div className={classes.stats}>
                                    <Timeline />
                                    <NavLink to='admin/storage'>
                                        <Locale>Check use history</Locale>
                                    </NavLink>
                                </div>
                            </CardFooter>
                        </Card>
                    </GridItem>
                    <GridItem xs={12} sm={6} md={6} lg={3}>
                        <Card>
                            <CardHeader color='success' stats icon>
                                <CardIcon color='success'>
                                    <Store />
                                </CardIcon>
                                <p className={classes.cardCategory}>
                                    <Locale>Revenue</Locale>
                                </p>
                                <h3 className={classes.cardTitle}>{weekly_revenue} {currency}</h3>
                            </CardHeader>
                            <CardFooter stats>
                                <div className={classes.stats}>
                                    <DateRange />
                                    <Locale>Last 7 Days</Locale>
                                </div>
                            </CardFooter>
                        </Card>
                    </GridItem>
                    <GridItem xs={12} sm={6} md={6} lg={3}>
                        <Card>
                            <CardHeader color='danger' stats icon>
                                <CardIcon color='danger'>
                                    <ShoppingCart />
                                </CardIcon>
                                <p className={classes.cardCategory}>
                                    <Locale>Sales</Locale>
                                </p>
                                <h3 className={classes.cardTitle}>{montly_sales}</h3>
                            </CardHeader>
                            <CardFooter stats>
                                <div className={classes.stats}>
                                    <DateRange />
                                    <Locale>This month</Locale>
                                </div>
                            </CardFooter>
                        </Card>
                    </GridItem>
                    <GridItem xs={12} sm={6} md={6} lg={3}>
                        <Card>
                            <CardHeader color='info' stats icon>
                                <CardIcon color='info'>
                                    <Person />
                                </CardIcon>
                                <p className={classes.cardCategory}>
                                    <Locale>User Count</Locale>
                                </p>
                                <h3 className={classes.cardTitle}>{user_count}</h3>
                            </CardHeader>
                            <CardFooter stats>
                                <div className={classes.stats}>
                                    <Group />
                                    <NavLink to='admin/users'>
                                        <Locale>Manage Users</Locale>
                                    </NavLink>
                                </div>
                            </CardFooter>
                        </Card>
                    </GridItem>
                </GridContainer>
                <GridContainer>
                    <GridItem xs={12} sm={12} md={12} lg={4}>
                        {this.renderWeeklyBalanceReloads()}
                    </GridItem>
                    <GridItem xs={12} sm={12} md={12} lg={4}>
                        {this.renderLastMonthlySalesCount()}
                    </GridItem>
                    <GridItem xs={12} sm={12} md={12} lg={4}>
                        <Card chart className={classes.cardHover}>
                            <CardHeader color='danger' className={classes.cardHeaderHover}>
                                <ChartistGraph
                                    className='ct-chart-white-colors'
                                    data={completedTasksChart.data}
                                    type='Line'
                                    options={completedTasksChart.options}
                                    listener={completedTasksChart.animation}
                                />
                            </CardHeader>
                            <CardBody>
                                <div className={classes.cardHoverUnder}>
                                    <Tooltip
                                        id='tooltip-top'
                                        title='Refresh'
                                        placement='bottom'
                                        classes={{ tooltip: classes.tooltip }}
                                    >
                                        <Button simple color='info' justIcon>
                                            <Refresh className={classes.underChartIcons} />
                                        </Button>
                                    </Tooltip>
                                    <Tooltip
                                        id='tooltip-top'
                                        title='Change Date'
                                        placement='bottom'
                                        classes={{ tooltip: classes.tooltip }}
                                    >
                                        <Button color='transparent' simple justIcon>
                                            <Edit className={classes.underChartIcons} />
                                        </Button>
                                    </Tooltip>
                                </div>
                                <h4 className={classes.cardTitle}>Completed Tasks</h4>
                                <p className={classes.cardCategory}>
                    Last Campaign Performance
                                </p>
                            </CardBody>
                            <CardFooter chart>
                                <div className={classes.stats}>
                                    <AccessTime /> campaign sent 2 days ago
                                </div>
                            </CardFooter>
                        </Card>
                    </GridItem>
                </GridContainer>
            </div>
        )
    }
}

const mapStateToProps = state => ({
    currency: state.server.currency
})

export default withStyles(dashboardStyle)(connect(mapStateToProps)(Dashboard))
