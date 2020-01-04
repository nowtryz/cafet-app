import React from 'react'
import axios from 'axios'
import Lock from '@material-ui/icons/Lock'
import { withStyles } from '@material-ui/core/styles'
import FormLabel from '@material-ui/core/FormLabel'
import CardHeader from '@dashboard/components/Card/CardHeader'
import CardIcon from '@dashboard/components/Card/CardIcon'
import CardBody from '@dashboard/components/Card/CardBody'
import Card from '@dashboard/components/Card/Card'
import GridContainer from '@dashboard/components/Grid/GridContainer'
import GridItem from '@dashboard/components/Grid/GridItem'
import Button from '@dashboard/components/CustomButtons/Button'
import userProfileStyle from '@dashboard/assets/jss/material-dashboard-pro-react/views/userProfileStyles'
import formStyles from '@dashboard/assets/jss/material-dashboard-pro-react/views/regularFormsStyle'
import Locale from '../../Locale'
import PasswordField from '../../inputs/PasswordField'
import { classesProptype, userProptype } from '../../../app-proptypes'
import _ from '../../../lang'
import { API_URL } from '../../../config'


const style = {
    ...userProfileStyle,
    ...formStyles,
}

class UserPasswordEdition extends React.Component {
    static propTypes = {
        classes: classesProptype.isRequired,
        user: userProptype.isRequired,
    }

    initialState = {
        password1: null,
        password2: null,
    }

    state = this.initialState

    pass1 = React.createRef()

    pass2 = React.createRef()

    async submitForm() {
        const { password1, password2 } = this.state
        const { user } = this.props

        if (!password2 || !password1 || password1 !== password2) return

        try {
            await axios.patch(`${API_URL}/server/users/${user.id}`, { password: password1 })
            this.setState(this.initialState)
            this.pass1.current.value = ''
            this.pass2.current.value = ''
        } catch (e) {
            // fixme
        }
    }

    render() {
        const { classes } = this.props
        const { password1, password2 } = this.state

        return (
            <Card>
                <CardHeader color="primary" icon>
                    <CardIcon color="primary">
                        <Lock />
                    </CardIcon>
                    <h4 className={classes.cardIconTitle}>
                        <Locale ns="admin_user_page">
                            Edit password
                        </Locale>
                    </h4>
                </CardHeader>
                <CardBody>
                    <form>
                        <GridContainer>
                            <GridItem xs={12} sm={12} md={3}>
                                <FormLabel className={classes.labelHorizontal}>
                                    <Locale ns="admin_user_page">
                                        New password
                                    </Locale>
                                </FormLabel>
                            </GridItem>
                            <GridItem xs={12} sm={12} md={9}>
                                <PasswordField
                                    inputRef={this.pass1}
                                    onChange={({ target }) => this.setState({ password1: target.value })}
                                />
                            </GridItem>
                        </GridContainer>
                        <GridContainer>
                            <GridItem xs={12} sm={12} md={3}>
                                <FormLabel className={classes.labelHorizontal}>
                                    <Locale ns="admin_user_page">
                                        Rewrite password
                                    </Locale>
                                </FormLabel>
                            </GridItem>
                            <GridItem xs={12} sm={12} md={9}>
                                <PasswordField
                                    inputRef={this.pass2}
                                    onChange={({ target }) => this.setState({ password2: target.value })}
                                    error={password2 && password1 !== password2}
                                    helperText={
                                        !password2 || password1 === password2 ? null
                                            : _('Passwords does not match')
                                    }
                                />
                            </GridItem>
                        </GridContainer>
                        <GridContainer justify="flex-end">
                            <GridItem xs={12} sm={12} md={9}>
                                <Button
                                    disabled={!password2 || password1 !== password2}
                                    onClick={(e) => this.submitForm(e)}
                                    color="rose"
                                    round
                                >
                                    <Locale>
                                        Submit
                                    </Locale>
                                </Button>
                            </GridItem>
                        </GridContainer>
                    </form>
                </CardBody>
            </Card>
        )
    }
}

export default withStyles(style)(UserPasswordEdition)
