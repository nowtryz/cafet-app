// eslint-disable-next-line import/prefer-default-export
import moment from 'moment'

export const rotate = (array, times) => {
    for (let i = 0; i < times % 7; i++) array.push(array.shift())
    return array
}

export const formatCalendar = ({
    year, month, day, hour, mins, secs,
}) => new Date(year, month - 1, day, hour, mins, secs)

export const momentFromCalendar = (calendar) => moment(formatCalendar(calendar))
