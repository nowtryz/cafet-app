import { useEffect } from 'react'
import axios from 'axios'
import useFetchReducer from './useFetchReducer'
import { API_URL } from '../config'


export default (path) => {
    const [state, dispatch] = useFetchReducer()
    const url = `${API_URL}${path}`

    useEffect(() => {
        const fetchData = async () => {
            dispatch({ type: 'FETCH_INIT' })

            try {
                const result = await axios.get(url)
                dispatch({ type: 'FETCH_SUCCESS', payload: result.data })
            } catch (error) {
                dispatch({ type: 'FETCH_FAILURE', payload: error })
            }
        }

        fetchData()
    }, [path])

    return state
}
