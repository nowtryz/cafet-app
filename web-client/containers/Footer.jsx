import React from 'react'
import PropTypes from 'prop-types'
import classNames from 'classnames'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'
import List from '@material-ui/core/List'
import ListItem from '@material-ui/core/ListItem'

import footerStyle from '@dashboard/assets/jss/material-dashboard-pro-react/components/footerStyle'

// eslint-disable-next-line react/prefer-stateless-function
class Footer extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        fluid: PropTypes.bool,
        white: PropTypes.bool,
    }

    static defaultProps = {
        fluid: false,
        white: false
    }

    render() {
        const { classes, fluid, white } = this.props
        const container = classNames({
            [classes.container]: !fluid,
            [classes.containerFluid]: fluid,
            [classes.whiteColor]: white
        })
        const anchor = classNames({
            [classes.a]: true,
            [classes.whiteColor]: white
        })
        const block = classNames({
            [classes.block]: true,
            [classes.whiteColor]: white
        })

        return (
            <footer className={classes.footer}>
                <div className={container}>
                    <div className={classes.left}>
                        <List className={classes.list}>
                            <ListItem className={classes.inlineBlock}>
                                <a href='#home' className={block}>
                                Home
                                </a>
                            </ListItem>
                        </List>
                    </div>
                    <p className={classes.right}>
                    &copy; 2018 - {1900 + new Date().getYear()} made with love by{' '}
                        <a href='https://nowtryz.net' className={anchor}>
                        Damien Djomby
                        </a>
                    </p>
                </div>
            </footer>
        )
    }
}

export default withStyles(footerStyle)(Footer)
