import React from 'react'
import { AppContainer } from 'react-hot-loader'
import { Provider } from 'react-redux'


const wrapper = (AppComponent, reduxStore) => (
    <Provider store={reduxStore}>
        <AppContainer>
            <AppComponent />
        </AppContainer>
    </Provider>
)

export default wrapper
