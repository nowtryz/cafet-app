import React from 'react'
import PropTypes from 'prop-types'
import cx from 'classnames'
import makeStyles from '@material-ui/core/styles/makeStyles'
import Grid from '@material-ui/core/Grid'
import Table from '@material-ui/core/Table'
import Hidden from '@material-ui/core/Hidden'
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
    noMargin: {
        marginTop: 0,
    },
    table: {
        [theme.breakpoints.up('md')]: {
            minWidth: 650,
        },
    },
    pointer: {
        cursor: 'pointer',
    },
}))

const SimpleTable = ({
    columns, data, onCellClick, isLoading, dataIdentifier, ns, hover, pointer, noMargin,
}) => {
    const classes = useStyles()

    return (
        <div className={cx(classes.root, { [classes.noMargin]: noMargin })}>
            <Table className={classes.table}>
                <TableHead>
                    <TableRow>
                        {columns.map((column) => (
                            <Hidden key={column.id} only={column.hidden}>
                                <TableCell
                                    align={column.numeric ? 'right' : 'left'}
                                    padding={column.disablePadding ? 'none' : 'default'}
                                >
                                    <Locale ns={ns}>
                                        {column.label}
                                    </Locale>
                                </TableCell>
                            </Hidden>
                        ))}
                    </TableRow>
                </TableHead>
                <TableBody>
                    {!(isLoading && data.length === 0) ? data.map((row) => (
                        <TableRow
                            hover={hover}
                            key={row[dataIdentifier]}
                            className={cx({ [classes.pointer]: pointer })}
                            onClick={(event) => onCellClick(event, row)}
                        >
                            {columns.map((cell) => (
                                <Hidden key={cell.id} only={cell.hidden}>
                                    <TableCell
                                        align={cell.numeric ? 'right' : 'left'}
                                        padding={cell.disablePadding ? 'none' : 'default'}
                                        onClick={
                                            (event) => onCellClick(event, row)
                                        }
                                        {...cell.cellProps}
                                    >
                                        {cell.render ? cell.render(row) : row[cell.id]}
                                    </TableCell>
                                </Hidden>
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
        disablePadding: PropTypes.bool,
        label: PropTypes.string.isRequired,
        render: PropTypes.func,
        hidden: PropTypes.oneOf(['xs', 'sm', 'md', 'lg', 'xl']),
    })).isRequired,
    data: PropTypes.arrayOf(PropTypes.any).isRequired,
    onCellClick: PropTypes.func,
    isLoading: PropTypes.bool,
    dataIdentifier: PropTypes.string,
    ns: PropTypes.string,
    hover: PropTypes.bool,
    pointer: PropTypes.bool,
    noMargin: PropTypes.bool,
}

SimpleTable.defaultProps = {
    onCellClick: () => null,
    isLoading: false,
    dataIdentifier: 'id',
    ns: 'default',
    hover: false,
    pointer: false,
    noMargin: false,
}

export default SimpleTable
