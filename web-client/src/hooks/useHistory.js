import { useEffect } from 'react'
import axios from 'axios'
import { API_URL } from '../config'
import { momentFromCalendar } from '../utils'
import useFetchReducer from './useFetchReducer'


const useHistory = (id) => {
    const [state, dispatch] = useFetchReducer()

    const url = `${API_URL}/cafet/clients/${id}`

    useEffect(() => {
        const fetchData = async () => {
            dispatch({ type: 'FETCH_INIT' })

            if (id) {
                try {
                    const [expensesResult, reloadsResult] = await axios.all([
                        axios.get(`${url}/expenses`),
                        axios.get(`${url}/reloads`),
                    ])
                    const data = [
                        ...expensesResult.data.map((item) => ({
                            ...item,
                            key: `e${item.id}`,
                        })),
                        ...reloadsResult.data.map((item) => ({
                            ...item,
                            total: item.amount,
                            key: `r${item.id}`,
                        })),
                    ]
                    data.sort(
                        (a, b) => (momentFromCalendar(a.date).isBefore(momentFromCalendar(b.date)) ? 1 : -1),
                    )
                    dispatch({ type: 'FETCH_SUCCESS', payload: data })
                } catch (error) {
                    dispatch({ type: 'FETCH_FAILURE', payload: error })
                }
            } else {
                dispatch({ type: 'FETCH_FAILURE' })
            }
        }

        fetchData()
    }, [id])

    return state
}

export default useHistory
