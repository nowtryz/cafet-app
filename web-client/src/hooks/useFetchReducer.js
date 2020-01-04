import { useReducer } from 'react'

const dataFetchReducer = (state, { type, payload = null }) => {
    switch (type) {
    case 'FETCH_INIT':
        return {
            ...state,
            isLoading: true,
            isError: false,
            error: null,
        }
    case 'FETCH_SUCCESS':
        return {
            ...state,
            isLoading: false,
            isError: false,
            data: payload,
        }
    case 'FETCH_FAILURE':
        return {
            ...state,
            isLoading: false,
            isError: true,
            error: payload,
        }
    default:
        throw new Error()
    }
}

export default (initialData = []) => useReducer(dataFetchReducer, {
    isLoading: true,
    isError: false,
    data: initialData,
    error: null,
})
