// eslint-disable-next-line import/prefer-default-export
export const rotate = (array, times) => {
    for (let i = 0; i < times % 7; i++) array.push(array.shift())
    return array
}