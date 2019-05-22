import { API_URL } from './config'

export const responseInterceptor = response => {
    return response
}
  
export const errorInterceptor = error => {
    if (401 === error.response.status && error.response.config.url != `${API_URL}/user/login`) {
        // handle redirection to login page
    }

    // This be supported as with polyfill
    // eslint-disable-next-line compat/compat
    return Promise.reject(error)
}