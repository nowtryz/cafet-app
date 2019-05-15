import { API_URL } from './config'

export const responseInterceptor = response => {
    console.log(response)
    return response
}
  
export const errorInterceptor = error => {
    if (401 === error.response.status && error.response.config.url != `${API_URL}/user/login`) {
        // handle redirection to login page
    }

    return Promise.reject(error)
}