import { useSelector } from 'react-redux'


export const useCurrency = () => useSelector((state) => state.server.currency.symbol)

export const useCurrencyFormatter = () => {
    const [currencyCode, langCode] = useSelector((state) => [
        state.server.currency.code,
        state.lang.lang_code,
    ])

    return (number) => number.toLocaleString(langCode, { style: 'currency', currency: currencyCode })
}
