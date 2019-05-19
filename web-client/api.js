import axios from 'axios'
import { API_URL } from 'config'

// eslint-disable-next-line import/prefer-default-export
export const registerUser = async (email, name, firstname, password) => {
    const response = await axios.post( `${API_URL}/user/register`, {
        email,
        name,
        firstname,
        password
    })

    return response.data
}