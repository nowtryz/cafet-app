import React from 'react'
import Buttons from './components/buttons'
import Message from './components/message'
import { APP_NAME } from './config'

const App = () =>
  <div>
    <h1>{APP_NAME}</h1>
    <Message />
    <Buttons />
  </div>

export default App