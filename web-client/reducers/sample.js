import { DO_SOMETHING } from '../actions'

const initialState = {
  message: 'Initial reducer message',
}

const sampleReducer = (state = initialState, { type, payload = null }) => {
  switch (type) {
    case DO_SOMETHING:
      return {
        ...state,
        message: payload
      }
    default:
      return state
  }
}

export default sampleReducer