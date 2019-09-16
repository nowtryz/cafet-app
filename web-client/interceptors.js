import store from 'reducers'
import { clearUser } from 'actions'
import { API_URL } from './config'

export const responseInterceptor = (response) => response

export const errorInterceptor = (error) => {
    if (error.response.status === 401 && error.response.config.url != `${API_URL}/user/login` && !store.getState().user.isLogging) {
        store.dispatch(clearUser())
    }

    // This be supported as with polyfill
    // eslint-disable-next-line compat/compat
    return Promise.reject(error)
}
