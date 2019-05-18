import PersonAdd from '@material-ui/icons/PersonAdd'
import Fingerprint from '@material-ui/icons/Fingerprint'
import LockOpen from '@material-ui/icons/LockOpen'

export default {
    register: {
        path: '/register',
        icon: PersonAdd,
        title: 'register'
    },
    login: {
        path: '/login',
        icon: Fingerprint,
        title: 'login'
    },
    lock: {
        path: '/lock',
        icon: LockOpen,
        title: 'lock'
    },
}