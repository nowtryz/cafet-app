const config = {
    presets: [
        '@babel/preset-env',
        '@babel/preset-react',
    ],
    plugins: [
        '@babel/plugin-proposal-class-properties',
        '@babel/plugin-transform-classes',
        'react-hot-loader/babel',
        ['module-resolver', {
            root: ['./web-client', './material-dashboard'],
        }],
    ],
}

module.exports = (app) => {
    app.cache(true)
    return config
}
