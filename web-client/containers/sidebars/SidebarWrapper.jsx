import React from 'react'
import PropTypes from 'prop-types'
// javascript plugin used to create scrollbars on windows
import PerfectScrollbar from 'perfect-scrollbar'

// We've created this component so we can have a ref to the wrapper of the links that appears in our sidebar.
// This was necessary so that we could initialize PerfectScrollbar on the links.
// There might be something with the Hidden component from material-ui, and we didn't have access to
// the links, and couldn't initialize the plugin.
class SidebarWrapper extends React.Component {
    static propTypes = {
        className: PropTypes.string.isRequired,
        children: PropTypes.oneOfType([
            PropTypes.arrayOf(PropTypes.element),
            PropTypes.element
        ]).isRequired
    }
    sidebarWrapper = React.createRef()
    ps = null

    componentDidMount() {
        if (navigator.platform.indexOf('Win') > -1) {
            this.ps = new PerfectScrollbar(this.sidebarWrapper.current, {
                suppressScrollX: true,
                suppressScrollY: false
            })
        }
    }
    componentWillUnmount() {
        if (navigator.platform.indexOf('Win') > -1) {
            this.ps.destroy()
        }
    }
    render() {
        const { className, children } = this.props
        return (
            <div className={className} ref={this.sidebarWrapper}>
                {children}
            </div>
        )
    }
}

export default SidebarWrapper