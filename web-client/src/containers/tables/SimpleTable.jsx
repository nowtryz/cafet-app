import React from 'react'
import PropTypes from 'prop-types'
import makeStyles from '@material-ui/core/styles/makeStyles'
import Grid from '@material-ui/core/Grid'
import Table from '@material-ui/core/Table'
import TableRow from '@material-ui/core/TableRow'
import TableHead from '@material-ui/core/TableHead'
import TableCell from '@material-ui/core/TableCell'
import TableBody from '@material-ui/core/TableBody'
import CircularProgress from '@material-ui/core/CircularProgress'
import Locale from '../Locale'

const useStyles = makeStyles((theme) => ({
    root: {
        width: '100%',
        marginTop: theme.spacing(3),
        overflowX: 'auto',
    },
    table: {
        minWidth: 650,
    },
}))

const SimpleTable = ({
    columns, data, onCellClick, isLoading,
}) => {
    const classes = useStyles()

    return (
        <div className={classes.root}>
            <Table className={classes.table}>
                <TableHead>
                    <TableRow>
                        {columns.map((column) => (
                            <TableCell
                                key={column.id}
                                align={column.numeric ? 'right' : 'left'}
                                padding={column.disablePadding ? 'none' : 'default'}
                            >
                                <Locale>
                                    {column.label}
                                </Locale>
                            </TableCell>
                        ))}
                    </TableRow>
                </TableHead>
                <TableBody>
                    {!(isLoading && data.length === 0) ? data.map((row) => (
                        <TableRow
                            hover
                            key={row.name}
                            onClick={onCellClick}
                        >
                            {columns.map((cell) => (
                                <TableCell
                                    key={cell.id}
                                    align={cell.numeric ? 'right' : 'left'}
                                    onClick={
                                        (event) => this.handleCellClick(event, row)
                                    }
                                    {...cell.cellProps}
                                >
                                    {cell.render ? cell.render(row) : row[cell.id]}
                                </TableCell>
                            ))}
                        </TableRow>
                    )) : (
                        <TableRow style={{ height: 490 }}>
                            <TableCell colSpan={columns.length}>
                                <Grid
                                    container
                                    justify="center"
                                    alignItems="center"
                                >
                                    <CircularProgress size={100} />
                                </Grid>
                            </TableCell>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        </div>
    )
}

SimpleTable.propTypes = {
    columns: PropTypes.arrayOf(PropTypes.shape({
        id: PropTypes.string.isRequired,
        numeric: PropTypes.bool,
        disablePadding: PropTypes.bool.isRequired,
        label: PropTypes.string.isRequired,
        render: PropTypes.func,
    })).isRequired,
    data: PropTypes.arrayOf(PropTypes.any).isRequired,
    onCellClick: PropTypes.func,
    isLoading: PropTypes.bool,
}

SimpleTable.defaultProps = {
    onCellClick: () => null,
    isLoading: false,
}

export default SimpleTable
