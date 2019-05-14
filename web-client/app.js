import React from 'react'
import HelloButton from './components/button'
import Message from './components/message'
import { APP_NAME } from './config'

const App = () =>
  <div>
    <h1>{APP_NAME}</h1>
    <Message />
    <HelloButton />
  </div>

export default App