import React from 'react'
import { connect } from 'react-redux'
import { PropTypes } from 'prop-types'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'
import InputAdornment from '@material-ui/core/InputAdornment'
import Icon from '@material-ui/core/Icon'

// @material-ui/icons
import Email from '@material-ui/icons/Email'
import Dialog from '@material-ui/core/Dialog'
import CircularProgress from '@material-ui/core/CircularProgress'

// core components
import GridContainer from '@dashboard/components/Grid/GridContainer'
import GridItem from '@dashboard/components/Grid/GridItem'
import CustomInput from '@dashboard/components/CustomInput/CustomInput'
import Button from '@dashboard/components/CustomButtons/Button'
import Card from '@dashboard/components/Card/Card'
import CardBody from '@dashboard/components/Card/CardBody'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardFooter from '@dashboard/components/Card/CardFooter'

import loginPageStyle from '@dashboard/assets/jss/material-dashboard-pro-react/views/loginPageStyle'
import background from '@dashboard/assets/img/login.jpeg'

import _ from 'lang'
import links from 'routes/auth'
import { login as loginAction } from 'actions'
import AuthLayout from '../layouts/auth'

const style = (theme) => ({
    ...loginPageStyle(theme),
    loadingDialog: {
        backgroundColor: 'transparent',
        boxShadow: 'none',
        width: 150,
        height: 150,
        textAlign: 'center',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
    },
    loadingBackdrop: {
        backdropFilter: 'blur(2px)',
    },
})

class LoginPage extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.any).isRequired,
        login: PropTypes.func.isRequired,
        isLogging: PropTypes.bool.isRequired,
    }

    state = {
        cardAnimation: 'cardHidden',
        email: '',
        password: '',
    }

    componentDidMount() {
    // we add a hidden class to the card and after 700 ms we delete it and the transition appears
        this.timeOutFunction = setTimeout(
            () => {
                this.setState({ cardAnimation: '' })
            },
            700,
        )
    }

    componentWillUnmount() {
        clearTimeout(this.timeOutFunction)
        this.timeOutFunction = null
    }

    validate = () => {
        const { login } = this.props
        const { email, password } = this.state

        login(email, password)
    }

    keyPressed = (e) => {
        // Number 13 is the "Enter" key on the keyboard
        if (e.nativeEvent.keyCode === 13) {
            this.validate(e)
        }
    }

    changeValue(e, field) {
        const { value } = e.target
        this.setState({
            [field]: value,
        })
    }

    render() {
        const { classes, isLogging, ...rest } = this.props
        const { cardAnimation, email, password } = this.state
        const title = _(links.login.title)

        return (
            <AuthLayout title={title} bgImage={background} {...rest}>
                <Dialog
                    disableBackdropClick
                    disableEscapeKeyDown
                    open={isLogging}
                    classes={{
                        paper: classes.loadingDialog,
                    }}
                    BackdropProps={{
                        classes: { root: classes.loadingBackdrop },
                    }}
                >
                    <CircularProgress size={100} />
                </Dialog>
                <div className={classes.container}>
                    <GridContainer justify="center" onKeyPress={this.keyPressed}>
                        <GridItem xs={12} sm={6} md={4}>
                            <form>
                                <Card login className={classes[cardAnimation]}>
                                    <CardHeader
                                        className={`${classes.cardHeader} ${classes.textCenter}`}
                                        color="rose"
                                    >
                                        <h4 className={classes.cardTitle}>{_('login')}</h4>
                                        <div className={classes.socialLine}>
                                            {/** [
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
                                            }) */}
                                        </div>
                                    </CardHeader>
                                    <CardBody>
                                        <CustomInput
                                            labelText={`${_('email')}...`}
                                            id="email"
                                            formControlProps={{
                                                fullWidth: true,
                                            }}
                                            inputProps={{
                                                value: email,
                                                autoFocus: true,
                                                onChange: (e) => this.changeValue(e, 'email'),
                                                endAdornment: (
                                                    <InputAdornment position="end">
                                                        <Email className={classes.inputAdornmentIcon} />
                                                    </InputAdornment>
                                                ),
                                            }}
                                        />
                                        <CustomInput
                                            labelText={`${_('password')}...`}
                                            id="password"
                                            formControlProps={{
                                                fullWidth: true,
                                            }}
                                            inputProps={{
                                                value: password,
                                                onChange: (e) => this.changeValue(e, 'password'),
                                                type: 'password',
                                                endAdornment: (
                                                    <InputAdornment position="end">
                                                        <Icon className={classes.inputAdornmentIcon}>
                                                            lock_outline
                                                        </Icon>
                                                    </InputAdornment>
                                                ),
                                            }}
                                        />
                                    </CardBody>
                                    <CardFooter className={classes.justifyContentCenter}>
                                        <Button color="rose" simple size="lg" block onClick={this.validate}>
                                            Let&#39;s Go
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

const mapStateToProps = (state) => ({
    isLogging: state.user.isLogging,
})

export default withStyles(style)(connect(mapStateToProps, {
    login: loginAction,
})(LoginPage))
