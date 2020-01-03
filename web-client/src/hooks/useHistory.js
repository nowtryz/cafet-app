import { useEffect, useReducer } from 'react'
import axios from 'axios'
import { API_URL } from '../config'
import { momentFromCalendar } from '../utils'

const dataFetchReducer = (state, action) => {
    switch (action.type) {
    case 'FETCH_INIT':
        return {
            ...state,
            isLoading: true,
            isError: false,
        }
    case 'FETCH_SUCCESS':
        return {
            ...state,
            isLoading: false,
            isError: false,
            data: action.payload,
        }
    case 'FETCH_FAILURE':
        return {
            ...state,
            isLoading: false,
            isError: true,
        }
    default:
        throw new Error()
    }
}

const useHistory = (id) => {
    const [state, dispatch] = useReducer(dataFetchReducer, {
        isLoading: false,
        isError: false,
        data: [],
    })

    const url = `${API_URL}/cafet/clients/${id}`

    useEffect(() => {
        const fetchData = async () => {
            dispatch({ type: 'FETCH_INIT' })

            if (id) {
                try {
                    const expensesResult = await axios.get(`${url}/expenses`)
                    const reloadsResult = await axios.get(`${url}/reloads`)
                    const data = [
                        ...expensesResult.data,
                        ...reloadsResult.data.map((item) => ({
                            ...item,
                            total: item.amount,
                        })),
                    ]
                    data.sort(
                        (a, b) => (momentFromCalendar(a.date).isBefore(momentFromCalendar(b.date)) ? 1 : -1),
                    )
                    dispatch({ type: 'FETCH_SUCCESS', payload: data })
                } catch (error) {
                    dispatch({ type: 'FETCH_FAILURE' })
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
