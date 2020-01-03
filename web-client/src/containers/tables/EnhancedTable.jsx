import React from 'react'
import PropTypes from 'prop-types'
import { withStyles } from '@material-ui/core/styles'
import Table from '@material-ui/core/Table'
import TableBody from '@material-ui/core/TableBody'
import TableCell from '@material-ui/core/TableCell'
import TablePagination from '@material-ui/core/TablePagination'
import TableRow from '@material-ui/core/TableRow'
import Checkbox from '@material-ui/core/Checkbox'

import { classesProptype } from '../../app-proptypes'

import EnhancedTableToolbar from './EnhancedTableToolbar'
import EnhancedTableHead from './EnhancedTableHead'
import Locale from '../Locale'


const desc = (a, b, orderBy) => {
    if (typeof a[orderBy] === 'string') {
        return a[orderBy].toLocaleLowerCase().localeCompare(b[orderBy].toLocaleLowerCase())
    }
    if (b[orderBy] < a[orderBy]) {
        return -1
    }
    if (b[orderBy] > a[orderBy]) {
        return 1
    }
    return 0
}

const stableSort = (array, cmp) => {
    const stabilizedThis = array.map((el, index) => [el, index])
    stabilizedThis.sort((a, b) => {
        const order = cmp(a[0], b[0])
        if (order !== 0) return order
        return a[1] - b[1]
    })
    return stabilizedThis.map((el) => el[0])
}

const getSorting = (order, orderBy) => (order === 'desc' ? (a, b) => desc(a, b, orderBy) : (a, b) => -desc(a, b, orderBy))

const styles = () => ({
    table: {
        minWidth: 1020,
    },
    tableWrapper: {
        overflowX: 'auto',
    },
    tableRow: {
        cursor: 'pointer',
    },
})

class EnhancedTable extends React.Component {
    static propTypes = {
        classes: classesProptype.isRequired,
        columns: EnhancedTableHead.rowsPropType.isRequired,
        data: PropTypes.arrayOf(PropTypes.any).isRequired,
        title: PropTypes.string.isRequired,
        dataIdentifier: PropTypes.string,
        onCellClick: PropTypes.func,
    }

    static defaultProps = {
        dataIdentifier: 'id',
        onCellClick: null,
    }

    state = {
        order: 'asc',
        orderBy: 'calories',
        selected: [],
        page: 0,
        rowsPerPage: 10,
    }

    handleRequestSort = (event, property) => {
        const orderBy = property
        let order = 'asc'
        const {
            orderBy: stateOrderBy,
            order: stateOrder,
        } = this.state

        if (stateOrderBy === property && stateOrder === 'asc') {
            order = 'desc'
        }

        this.setState({ order, orderBy, page: 0 })
    }

    handleSelectAllClick = (event) => {
        if (event.target.checked) {
            const { data, dataIdentifier } = this.props
            this.setState({ selected: data.map((n) => n[dataIdentifier]) })
            return
        }
        this.setState({ selected: [] })
    }

    handleClick = (event, id) => {
        const { selected } = this.state
        const selectedIndex = selected.indexOf(id)
        let newSelected = []

        if (selectedIndex === -1) {
            newSelected = newSelected.concat(selected, id)
        } else if (selectedIndex === 0) {
            newSelected = newSelected.concat(selected.slice(1))
        } else if (selectedIndex === selected.length - 1) {
            newSelected = newSelected.concat(selected.slice(0, -1))
        } else if (selectedIndex > 0) {
            newSelected = newSelected.concat(
                selected.slice(0, selectedIndex),
                selected.slice(selectedIndex + 1),
            )
        }

        this.setState({ selected: newSelected })
    }

    handleCellClick = (event, id) => {
        const { onCellClick } = this.props
        return (onCellClick || this.handleClick)(event, id)
    }

    handleChangePage = (event, page) => {
        this.setState({ page })
    }

    handleChangeRowsPerPage = (event) => {
        this.setState({ rowsPerPage: event.target.value })
    }

    isSelected = (id) => {
        const { selected } = this.state
        return selected.indexOf(id) !== -1
    }

    render() {
        const {
            classes, columns, data, dataIdentifier, title,
        } = this.props
        const {
            order, orderBy, selected, rowsPerPage, page,
        } = this.state
        const emptyRows = rowsPerPage - Math.min(rowsPerPage, data.length - page * rowsPerPage)

        return (
            <>
                <EnhancedTableToolbar numSelected={selected.length} title={title} />
                <div className={classes.tableWrapper}>
                    <Table className={classes.table} aria-labelledby="tableTitle">
                        <EnhancedTableHead
                            numSelected={selected.length}
                            order={order}
                            orderBy={orderBy}
                            onSelectAllClick={this.handleSelectAllClick}
                            onRequestSort={this.handleRequestSort}
                            rowCount={data.length}
                            rows={columns}
                        />
                        <TableBody>
                            {stableSort(data, getSorting(order, orderBy))
                                .slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                                .map((row) => {
                                    const isSelected = this.isSelected(row[dataIdentifier])
                                    return (
                                        <TableRow
                                            hover
                                            className={classes.tableRow}
                                            role="checkbox"
                                            aria-checked={isSelected}
                                            tabIndex={-1}
                                            key={row[dataIdentifier]}
                                            selected={isSelected}
                                        >
                                            <TableCell padding="checkbox">
                                                <Checkbox
                                                    checked={isSelected}
                                                    onClick={(event) => this.handleClick(event, row[dataIdentifier])}
                                                />
                                            </TableCell>
                                            {columns.map((cell) => (
                                                <TableCell
                                                    key={cell.id}
                                                    align={cell.numeric ? 'right' : 'left'}
                                                    onClick={
                                                        (event) => this.handleCellClick(event, row[dataIdentifier])
                                                    }
                                                    {...cell.cellProps}
                                                >
                                                    {cell.render ? cell.render(row) : row[cell.id]}
                                                </TableCell>
                                            ))}
                                        </TableRow>
                                    )
                                })}
                            {emptyRows > 0 && (
                                <TableRow style={{ height: 49 * emptyRows }}>
                                    <TableCell colSpan={columns.length} />
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </div>
                <TablePagination
                    rowsPerPageOptions={[5, 10, 25, 50].concat(data.length > 50 ? [data.length] : [])}
                    component="div"
                    count={data.length}
                    rowsPerPage={rowsPerPage}
                    page={page}
                    labelRowsPerPage={(
                        <Locale>
                            Rows per page:
                        </Locale>
                    )}
                    labelDisplayedRows={({ from, to, count }) => (
                        <Locale from={from} to={to === -1 ? count : to} count={count}>
                            %(from)s-%(to)s of %(count)s
                        </Locale>
                    )}
                    backIconButtonProps={{
                        'aria-label': 'Previous Page',
                    }}
                    nextIconButtonProps={{
                        'aria-label': 'Next Page',
                    }}
                    onChangePage={this.handleChangePage}
                    onChangeRowsPerPage={this.handleChangeRowsPerPage}
                />
            </>
        )
    }
}

export default withStyles(styles)(EnhancedTable)
