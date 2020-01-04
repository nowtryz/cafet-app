const isClient = typeof window !== 'undefined'

export const APP_NAME = 'Cafet APP!'
export const SERVER_URL = isClient ? window.SERVER_URL.replace(/\/$/g, '') : ''
export const API_URL = `${SERVER_URL}/api/v1`
export const isProd = process.env.NODE_ENV === 'production'
