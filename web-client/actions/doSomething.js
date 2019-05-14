import { createAction } from 'redux-actions'

export const DO_SOMETHING = 'DO_SOMETHING'

export const doSomething = (message = 'hello') => dispatch => {
    dispatch({
        type: DO_SOMETHING,
        payload: message
    })
}