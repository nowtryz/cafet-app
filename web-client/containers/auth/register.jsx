import React from 'react'
import { connect } from 'react-redux'
import Helmet from 'react-helmet'
import PropTypes from 'prop-types'

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles'
import InputAdornment from '@material-ui/core/InputAdornment'
import Checkbox from '@material-ui/core/Checkbox'
import FormControlLabel from '@material-ui/core/FormControlLabel'
import Icon from '@material-ui/core/Icon'
import Dialog from '@material-ui/core/Dialog'
import CircularProgress from '@material-ui/core/CircularProgress'

// @material-ui/icons
import Timeline from '@material-ui/icons/Timeline'
import TouchApp from '@material-ui/icons/TouchApp'
import Face from '@material-ui/icons/Face'
import Email from '@material-ui/icons/Email'
import MonetizationOn from '@material-ui/icons/MonetizationOn'
// import LockOutline from '@material-ui/icons/LockOutline';
import Check from '@material-ui/icons/Check'

// core components
import GridContainer from '@dashboard/components/Grid/GridContainer'
import GridItem from '@dashboard/components/Grid/GridItem'
import Button from '@dashboard/components/CustomButtons/Button'
import CustomInput from '@dashboard/components/CustomInput/CustomInput'
import InfoArea from '@dashboard/components/InfoArea/InfoArea'
import Card from '@dashboard/components/Card/Card'
import CardBody from '@dashboard/components/Card/CardBody'

import registerPageStyle from '@dashboard/assets/jss/material-dashboard-pro-react/views/registerPageStyle'
import background from '@dashboard/assets/img/register.jpeg'

import links from 'routes/auth'
import { registerUser } from 'api'
import { login as loginAction } from 'actions'
import _ from 'lang'

import AuthLayout from '../layouts/auth'

class RegisterPage extends React.Component {
    static propTypes = {
        classes: PropTypes.objectOf(PropTypes.string).isRequired,
        lang: PropTypes.objectOf(PropTypes.any).isRequired,
        login: PropTypes.func.isRequired,
    }

    state = {
        checked: [],
        termsAccepted: false,
        isValidating: false,
        emailValidation: undefined,
        familyNameValidation: undefined,
        firstnameValidation: undefined,
        passwordValidation: undefined,
        email: '',
        familyName: '',
        firstname: '',
        password: '',
    }

    getFields() {
        const {
            emailValidation, familyNameValidation, firstnameValidation, passwordValidation,
        } = this.state

        return ([
            {
                name: _('firstName'),
                icone: Face,
                id: 'firstname',
                inputProps: {
                    error: (firstnameValidation != null),
                },
                helperText: firstnameValidation,
            }, {
                name: _('name'),
                icone: Face,
                id: 'familyName',
                inputProps: {
                    error: (familyNameValidation != null),
                },
                helperText: familyNameValidation,
            }, {
                name: _('email'),
                icone: Email,
                id: 'email',
                inputProps: {
                    error: (emailValidation != null),
                },
                helperText: emailValidation,
            }, {
                name: _('password'),
                icone: Icon,
                iconeName: 'lock_outline',
                id: 'password',
                inputProps: {
                    type: 'password',
                    error: (passwordValidation != null),
                },
                helperText: passwordValidation,
            },
        ])
    }

    keyPressed = (e) => {
        // Number 13 is the "Enter" key on the keyboard
        if (event.keyCode === 13) {
            this.validate(e)
        }
    }

    validate = async () => {
        const { login } = this.props
        const {
            email, familyName, firstname, password, isValidating,
        } = this.state

        if (isValidating) return
        this.setState({ isValidating: true })

        const emailValidation = email ? undefined : _('required')
        const familyNameValidation = familyName ? undefined : _('required')
        const firstnameValidation = firstname ? undefined : _('required')
        const passwordValidation = password ? undefined : _('required')
        this.setState({
            emailValidation, familyNameValidation, firstnameValidation, passwordValidation,
        })
        if (emailValidation || familyNameValidation || firstnameValidation || passwordValidation) {
            this.setState({ isValidating: false })
            return
        }

        try {
            await registerUser(email, familyName, firstname, password)
            login(email, password)
        } catch (err) {
            if (err.response !== undefined && err.response.data.conflicts !== undefined) {
                this.handleConflicts(err.response.data.conflicts)
            }
        }

        this.setState({ isValidating: false })
    }

    handleToggle(value) {
        const { checked } = this.state
        const currentIndex = checked.indexOf(value)
        const newChecked = [...checked]

        if (currentIndex === -1) {
            newChecked.push(value)
        } else {
            newChecked.splice(currentIndex, 1)
        }

        this.setState({
            checked: newChecked,
            termsAccepted: newChecked.includes(1),
        })
    }

    changeValue(e, field) {
        const { value } = e.target
        this.setState({
            [field]: value,
        })
    }

    handleConflicts(conflicts) {
        if (conflicts.email !== undefined) {
            if (conflicts.email === 'duplicated') {
                this.setState({ emailValidation: _('emailDuplicated') })
            } else if (conflicts.email === 'not valid') {
                this.setState({ emailValidation: _('emailInvalid') })
            }
        }
    }

    renderInput(field) {
        const { classes } = this.props

        return (
            <React.Fragment key={field.name}>
                <CustomInput
                    formControlProps={{
                        fullWidth: true,
                        required: true,
                        className: classes.customFormControlClasses,
                    }}
                    inputProps={{
                        onChange: (e) => this.changeValue(e, field.id),
                        startAdornment: (
                            <InputAdornment
                                position="start"
                                className={classes.inputAdornment}
                            >
                                <field.icone className={classes.inputAdornmentIcon}>
                                    {field.iconeName}
                                </field.icone>
                            </InputAdornment>
                        ),
                        placeholder: `${field.name}...`,
                        ...field.inputProps,
                    }}
                    helpText={field.helperText}
                    {...field.inputProps}
                />
            </React.Fragment>
        )
    }

    render() {
        const { classes, ...rest } = this.props
        const title = _(links.register.title)
        const { termsAccepted, isValidating } = this.state

        return (
            <AuthLayout title={title} bgImage={background} {...rest}>
                <Helmet title={title} />
                <Dialog
                    disableBackdropClick
                    disableEscapeKeyDown
                    open={isValidating}
                    PaperProps={{
                        style: {
                            backgroundColor: 'transparent',
                            boxShadow: 'none',
                            width: 150,
                            height: 150,
                            textAlign: 'center',
                            display: 'flex',
                            justifyContent: 'center',
                            alignItems: 'center',
                        },
                    }}
                >
                    <CircularProgress size={100} />
                </Dialog>
                <div className={classes.container}>
                    <GridContainer justify="center">
                        <GridItem xs={12} sm={12} md={10}>
                            <Card className={classes.cardSignup}>
                                <h2 className={classes.cardTitle}>{_('register')}</h2>
                                <CardBody onKeyPress={this.keyPressed}>
                                    <GridContainer justify="center">
                                        <GridItem xs={12} sm={12} md={5}>
                                            <InfoArea
                                                title={_('statistics')}
                                                description={_('statistics', 'register_page')}
                                                icon={Timeline}
                                                iconColor="rose"
                                            />
                                            <InfoArea
                                                title={_('balance')}
                                                description={_('balance', 'register_page')}
                                                icon={MonetizationOn}
                                                iconColor="primary"
                                            />
                                            <InfoArea
                                                title={_('easy to use')}
                                                description={_('easy to use', 'register_page')}
                                                icon={TouchApp}
                                                iconColor="info"
                                            />
                                        </GridItem>
                                        <GridItem xs={12} sm={8} md={5}>
                                            <div className={classes.center}>
                                                <Button justIcon round color="twitter">
                                                    <i className="fab fa-twitter" />
                                                </Button>
                                                {' '}
                                                <Button justIcon round color="facebook">
                                                    <i className="fab fa-facebook-f" />
                                                </Button>
                                                {' '}
                                                <h4 className={classes.socialTitle}>{_('or be casual')}</h4>
                                            </div>
                                            <form className={classes.form}>
                                                {this.getFields().map((field) => this.renderInput(field))}
                                                <FormControlLabel
                                                    classes={{
                                                        root: classes.checkboxLabelControl,
                                                        label: classes.checkboxLabel,
                                                    }}
                                                    control={(
                                                        <Checkbox
                                                            tabIndex={-1}
                                                            onClick={() => this.handleToggle(1)}
                                                            checkedIcon={
                                                                <Check className={classes.checkedIcon} />
                                                            }
                                                            icon={<Check className={classes.uncheckedIcon} />}
                                                            classes={{
                                                                checked: classes.checked,
                                                                root: classes.checkRoot,
                                                            }}
                                                        />
                                                    )}
                                                    label={(
                                                        <span>
                                                            {_('agreements', 'register_page')}
                                                            <a href="#pablo">{_('terms', 'register_page')}</a>.
                                                        </span>
                                                    )}
                                                />
                                                <div className={classes.center}>
                                                    <Button disabled={!termsAccepted || isValidating} round color="primary" onClick={this.validate}>
                                                        {_('getStarted')}
                                                    </Button>
                                                </div>
                                            </form>
                                        </GridItem>
                                    </GridContainer>
                                </CardBody>
                            </Card>
                        </GridItem>
                    </GridContainer>
                </div>
            </AuthLayout>
        )
    }
}

export default withStyles(registerPageStyle)(connect(null, {
    login: loginAction,
})(RegisterPage))