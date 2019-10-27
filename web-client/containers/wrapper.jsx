import React from 'react'
import { AppContainer } from 'react-hot-loader'
import { Provider } from 'react-redux'


const wrapper = (AppComponent, reduxStore, history) => (
    <AppContainer>
        <Provider store={reduxStore}>
            <AppComponent history={history} />
        </Provider>
    </AppContainer>
)

export default wrapper
