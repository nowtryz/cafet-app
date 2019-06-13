import axios from 'axios'

import { SERVER_INFO_LOADED } from 'constants'
import { API_URL } from '../config'

// eslint-disable-next-line import/prefer-default-export
export const loadServerConfig = () => async dispach => {
    try {
        const response = await axios.get(`${API_URL}/server/information`)

        dispach({
            type: SERVER_INFO_LOADED,
            payload: response.data
        })
    } catch (err) {
        // TODO handle error
    }
    
}