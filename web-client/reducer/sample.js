import { DO_SOMETHING } from '../action/doSomething'

const initialState = {
  message: 'Initial reducer message',
}

const sampleReducer = (state = initialState, { type, payload }) => {
  switch (type) {
    case DO_SOMETHING:
      state.message = payload
      return {
        ...state,
        message: payload
      }
    default:
      return state
  }
}

export default sampleReducer