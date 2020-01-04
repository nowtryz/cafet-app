import { useEffect } from 'react'
import axios from 'axios'
import useFetchReducer from './useFetchReducer'
import { API_URL, SERVER_URL } from '../config'


export default (id) => {
    const [{ data, ...state }, dispatch] = useFetchReducer({
        expense: {},
        details: [],
    })
    const url = `${API_URL}/cafet/expenses/${id}`

    useEffect(() => {
        const fetchData = async () => {
            dispatch({ type: 'FETCH_INIT' })

            try {
                const [expenseResult, detailsResult] = await axios.all([
                    axios.get(url),
                    axios.get(`${url}/details`),
                ])

                dispatch({
                    type: 'FETCH_SUCCESS',
                    payload: {
                        expense: expenseResult.data,
                        details: detailsResult.data,
                    },
                })

                const detailsImages = await axios.all(detailsResult.data.map((d) => axios.head(
                    `${SERVER_URL}/image/${d.type === 'ProductBought' ? `product/${d.product}` : `formula/${d.formula}`}`,
                    {
                        validateStatus: (status) => (status >= 200 && status < 300) || status === 404,
                    },
                )))

                dispatch({
                    type: 'FETCH_SUCCESS',
                    payload: {
                        expense: expenseResult.data,
                        details: detailsResult.data.map((detail, i) => {
                            if (detailsImages[i].status !== 404) {
                                return {
                                    ...detail,
                                    img: detailsImages[i].config.url,
                                }
                            }
                            return detail
                        }),
                    },
                })
            } catch (error) {
                dispatch({
                    type: 'FETCH_FAILURE',
                    payload: error,
                })
            }
        }

        fetchData()
    }, [id])

    const { expense, details } = data
    return {
        expense,
        details,
        ...state,
    }
}
