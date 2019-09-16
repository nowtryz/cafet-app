import React from 'react'
import cx from 'classnames'

// Material UI
import { makeStyles } from '@material-ui/core/styles'
import Tab from '@material-ui/core/Tab'
import Tabs from '@material-ui/core/Tabs'

import Dashboard from '@material-ui/icons/Dashboard'
import Schedule from '@material-ui/icons/Schedule'

// Material Dashboard
import styles from '@dashboard/assets/jss/material-dashboard-pro-react/components/navPillsStyle'

const useStyle = makeStyles(styles)

export default ({ onChange }) => {
    const classes = useStyle()

    return (
        <Tabs
            classes={{
                root: classes.root,
                fixed: classes.fixed,
                flexContainer: cx(
                    classes.flexContainer,
                    classes.horizontalDisplay,
                ),
                indicator: classes.displayNone,
            }}
            centered
            value={userTab}
            onChange={onChange}
        >
            <Tab
                label="Dashboard"
                icon={Dashboard}
                classes={{
                    root: cx(
                        classes.pills,
                        classes.horizontalPills,
                        classes.pillsWithIcons,
                    ),
                    labelContainer: classes.labelContainer,
                    label: classes.label,
                    selected: classes.primary,
                }}
            />
            <Tab
                label="Schedule"
                icon={Schedule}
            />
        </Tabs>
    )
}
