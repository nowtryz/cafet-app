import React from 'react'
import Helmet from 'react-helmet'
import { PropTypes } from 'prop-types'

import background from 'assets/img/login.jpeg'
import links from 'routes/auth'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'
import InputAdornment from '@material-ui/core/InputAdornment'
import Icon from '@material-ui/core/Icon'

// @material-ui/icons
import Face from '@material-ui/icons/Face'
import Email from '@material-ui/icons/Email'
// import LockOutline from "@material-ui/icons/LockOutline";

// core components
import GridContainer from 'components/Grid/GridContainer'
import GridItem from 'components/Grid/GridItem'
import CustomInput from 'components/CustomInput/CustomInput'
import Button from 'components/CustomButtons/Button'
import Card from 'components/Card/Card'
import CardBody from 'components/Card/CardBody'
import CardHeader from 'components/Card/CardHeader'
import CardFooter from 'components/Card/CardFooter'

import loginPageStyle from 'assets/jss/material-dashboard-pro-react/views/loginPageStyle'
import AuthLayout from '../layouts/auth'


class LoginPage extends React.Component {
    constructor(props) {
        super(props)
        // we use this to make the card to appear after the page has been rendered
        this.state = {
            cardAnimaton: 'cardHidden'
        }
    }
    componentDidMount() {
    // we add a hidden class to the card and after 700 ms we delete it and the transition appears
        this.timeOutFunction = setTimeout(
            function() {
                this.setState({ cardAnimaton: '' })
            }.bind(this),
            700
        )
    }
    componentWillUnmount() {
        clearTimeout(this.timeOutFunction)
        this.timeOutFunction = null
    }
    render() {
        const { classes, lang, ...rest } = this.props
        const { cardAnimaton } = this.state
        const title = lang[links.login.title]

        return (
            <AuthLayout title={title} bgImage={background} {...rest}>
                <Helmet title={title} />
                <div className={classes.container}>
                    <GridContainer justify="center">
                        <GridItem xs={12} sm={6} md={4}>
                            <form>
                                <Card login className={classes[cardAnimaton]}>
                                    <CardHeader
                                        className={`${classes.cardHeader} ${classes.textCenter}`}
                                        color="rose"
                                    >
                                        <h4 className={classes.cardTitle}>Log in</h4>
                                        <div className={classes.socialLine}>
                                            {[
                                                'fab fa-facebook-square',
                                                'fab fa-twitter',
                                                'fab fa-google-plus'
                                            ].map(prop => {
                                                return (
                                                    <Button
                                                        color="transparent"
                                                        justIcon
                                                        key={prop}
                                                        className={classes.customButtonClass}
                                                    >
                                                        <i className={prop} />
                                                    </Button>
                                                )
                                            })}
                                        </div>
                                    </CardHeader>
                                    <CardBody>
                                        <CustomInput
                                            labelText="First Name.."
                                            id="firstname"
                                            formControlProps={{
                                                fullWidth: true
                                            }}
                                            inputProps={{
                                                endAdornment: (
                                                    <InputAdornment position="end">
                                                        <Face className={classes.inputAdornmentIcon} />
                                                    </InputAdornment>
                                                )
                                            }}
                                        />
                                        <CustomInput
                                            labelText="Email..."
                                            id="email"
                                            formControlProps={{
                                                fullWidth: true
                                            }}
                                            inputProps={{
                                                endAdornment: (
                                                    <InputAdornment position="end">
                                                        <Email className={classes.inputAdornmentIcon} />
                                                    </InputAdornment>
                                                )
                                            }}
                                        />
                                        <CustomInput
                                            labelText="Password"
                                            id="password"
                                            formControlProps={{
                                                fullWidth: true
                                            }}
                                            inputProps={{
                                                endAdornment: (
                                                    <InputAdornment position="end">
                                                        <Icon className={classes.inputAdornmentIcon}>
                            lock_outline
                                                        </Icon>
                                                    </InputAdornment>
                                                )
                                            }}
                                        />
                                    </CardBody>
                                    <CardFooter className={classes.justifyContentCenter}>
                                        <Button color="rose" simple size="lg" block>
                                            Let&quote;s Go
                                        </Button>
                                    </CardFooter>
                                </Card>
                            </form>
                        </GridItem>
                    </GridContainer>
                </div>
      
            </AuthLayout>
        )
    }
}

LoginPage.propTypes = {
    classes: PropTypes.PropTypes.objectOf(PropTypes.object()).isRequired,
    lang: PropTypes.PropTypes.objectOf(PropTypes.string).isRequired
}

export default withStyles(loginPageStyle)(LoginPage)