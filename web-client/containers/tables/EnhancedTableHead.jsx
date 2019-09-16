import React from 'react'
import PropTypes from 'prop-types'

// Material UI
import TableHead from '@material-ui/core/TableHead'
import TableCell from '@material-ui/core/TableCell'
import TableRow from '@material-ui/core/TableRow'
import TableSortLabel from '@material-ui/core/TableSortLabel'
import Tooltip from '@material-ui/core/Tooltip'
import Checkbox from '@material-ui/core/Checkbox'

import _ from 'lang'
import Locale from '../Locale'

class EnhancedTableHead extends React.Component {
    static rowsPropType = PropTypes.arrayOf(PropTypes.shape({
        id: PropTypes.string.isRequired,
        numeric: PropTypes.bool.isRequired,
        component: PropTypes.bool.isRequired,
        disablePadding: PropTypes.bool.isRequired,
        label: PropTypes.string.isRequired,
        render: PropTypes.func,
        sorter: PropTypes.func,
    }))

    static propTypes = {
        numSelected: PropTypes.number.isRequired,
        onRequestSort: PropTypes.func.isRequired,
        onSelectAllClick: PropTypes.func.isRequired,
        order: PropTypes.string.isRequired,
        orderBy: PropTypes.string.isRequired,
        rowCount: PropTypes.number.isRequired,
        rows: EnhancedTableHead.rowsPropType.isRequired,
    }

    createSortHandler = (property) => (event) => {
        const { onRequestSort } = this.props
        onRequestSort(event, property)
    };

    render() {
        const {
            onSelectAllClick, order, orderBy, numSelected, rowCount, rows,
        } = this.props

        return (
            <TableHead>
                <TableRow>
                    <TableCell padding="checkbox">
                        <Checkbox
                            indeterminate={numSelected > 0 && numSelected < rowCount}
                            checked={numSelected === rowCount}
                            onChange={onSelectAllClick}
                        />
                    </TableCell>
                    {rows.map(
                        (row) => (
                            <TableCell
                                key={row.id}
                                align={row.numeric ? 'right' : 'left'}
                                padding={row.disablePadding ? 'none' : 'default'}
                                sortDirection={orderBy === row.id ? order : false}
                            >
                                <Tooltip
                                    title={_('Sort')}
                                    placement={row.numeric ? 'bottom-end' : 'bottom-start'}
                                    enterDelay={300}
                                >
                                    <TableSortLabel
                                        active={orderBy === row.id}
                                        direction={order}
                                        onClick={this.createSortHandler(row.id, row.sorter)}
                                    >
                                        <Locale>
                                            {row.label}
                                        </Locale>
                                    </TableSortLabel>
                                </Tooltip>
                            </TableCell>
                        ),
                        this,
                    )}
                </TableRow>
            </TableHead>
        )
    }
}

export default EnhancedTableHead
