let isClient = typeof window !== 'undefined'

export const APP_NAME = 'Cafet APP!'
export const API_URL = `${isClient ? window.SERVER_URL.replace(/\/$/g, '') : ''}/api/v1`
export const isProd = process.env.NODE_ENV === 'production'