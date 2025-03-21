/* eslint-disable @typescript-eslint/no-unused-vars */
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import React, { useMemo, useState, useEffect, useCallback } from 'react';
import SelectionActionModal from '@/components/table/SelectionActionModal';
import { SponsorFilter } from '@/components/table/SponsorFilter';
import { FiscalYearFilter } from '@/components/table/FiscalYearFilter';
import DownloadProgressModal from '@/components/modal/downloadprogressmodal';
import useDownload from '@/hooks/use-download';
import { ChildData } from '@/types/table';
import useFetchTableData from '@/hooks/use-fetch-table-data';
import useDelete from '@/hooks/use-delete';
import { UploadModal } from '@/components/modal/uploadmodal';
import Modal from 'react-bootstrap/esm/Modal';
import TextLink from '@/components/text-link';

// Remove React Table imports
// Keep only what we need for our types
type SortingState = {
    id: string;
    desc: boolean;
}[];



export default function Dashboard() {
    const [rowSelection, setRowSelection] = useState<Record<string, boolean>>({});
    const [sorting, setSorting] = useState<SortingState>([]);
    const [filterFiscalYear, setFilterFiscalYear] = useState<string>('0');
    const [filterValue, setFilterValue] = useState<string>('a');
    const [searchQuery, setSearchQuery] = useState<string>('');
    const [downloadModalShowed, setDownloadModalShowed] = useState(false);
    const [debouncedQuery, setDebouncedQuery] = useState("");
    const { refetch, data = [], isLoading } = useFetchTableData({ sponsorCategory: filterValue, fiscalYear: filterFiscalYear, searchQuery: debouncedQuery });

    const [currentPage, setCurrentPage] = useState(0);
    const [pageSize, setPageSize] = useState(10);

    const { mutate: mutateDownload, progress, startDownload, startDownloadAsync, status, download_id } = useDownload({ setState: setDownloadModalShowed, refetchData: refetch });
    const { mutate: mutateDelete } = useDelete({ refetchData: refetch });
    const handleDownload = (rowData: ChildData) => {
        setDownloadModalShowed(true);
        mutateDownload({ child_idn: [rowData.child_idn], fiscal_year: rowData.fiscal_year });
    };
    const handleDelete = (rowData: ChildData) => {
        mutateDelete({ deleteList: [{ child_idn: rowData.child_idn, fiscal_year: rowData.fiscal_year }] });
    };
    const [showUploadModal, setShowUploadModal] = useState(false);

    const sortData = useCallback((data: ChildData[]) => {
        if (sorting.length === 0) return data;

        const sortField = sorting[0].id;
        const sortDirection = sorting[0].desc ? -1 : 1;

        return [...data].sort((a, b) => {
            const aValue = a[sortField as keyof ChildData];
            const bValue = b[sortField as keyof ChildData];

            if (typeof aValue === 'string' && typeof bValue === 'string') {
                return sortDirection * aValue.localeCompare(bValue);
            }

            if (aValue < bValue) return -1 * sortDirection;
            if (aValue > bValue) return 1 * sortDirection;
            return 0;
        });
    }, [sorting]);

    const displayData = useMemo(() => {
        const sortedData = sortData(data);
        const start = currentPage * pageSize;
        const end = start + pageSize;
        return sortedData.slice(start, end);
    }, [data, sortData, currentPage, pageSize]);

    const toggleSort = useCallback((columnId: string) => {
        setSorting(prev => {
            if (prev.length > 0 && prev[0].id === columnId) {
                if (prev[0].desc) {
                    return [];
                }
                return [{ id: columnId, desc: true }];
            }
            return [{ id: columnId, desc: false }];
        });
    }, []);

    const toggleRowSelection = useCallback((localIndex: number) => {
        const globalIndex = currentPage * pageSize + localIndex;
        setRowSelection(prev => ({
            ...prev,
            [globalIndex]: !prev[globalIndex]
        }));
    }, [currentPage, pageSize]);

    const toggleSelectAll = useCallback(() => {
        if (displayData.every((_, idx) => rowSelection[currentPage * pageSize + idx])) {
            const newSelection = { ...rowSelection };
            displayData.forEach((_, idx) => {
                delete newSelection[currentPage * pageSize + idx];
            });
            setRowSelection(newSelection);
        } else {
            const newSelection = { ...rowSelection };
            displayData.forEach((_, idx) => {
                newSelection[currentPage * pageSize + idx] = true;
            });
            setRowSelection(newSelection);
        }
    }, [displayData, rowSelection, currentPage, pageSize]);

    const allRowsSelected = useMemo(() => {
        return displayData.length > 0 &&
            displayData.every((_, idx) => !!rowSelection[currentPage * pageSize + idx]);
    }, [displayData, rowSelection, currentPage, pageSize]);

    const totalPages = useMemo(() => {
        return Math.ceil(data.length / pageSize);
    }, [data, pageSize]);


    useEffect(() => {
        setCurrentPage(0);
        setRowSelection({});
    }, [filterValue, filterFiscalYear]);

    useEffect(() => {
        const handler = setTimeout(() => {
            setDebouncedQuery(searchQuery);
        }, 500);

        return () => clearTimeout(handler);
    }, [searchQuery]);

    const handleSelectAllData = useCallback(() => {
        const allSelected = data.length > 0 && data.length === Object.keys(rowSelection).length &&
            data.every((_, idx) => !!rowSelection[idx]);

        if (allSelected) {
            setRowSelection({});
        } else {
            const newSelection: Record<string, boolean> = {};
            data.forEach((_, idx) => {
                newSelection[idx] = true;
            });
            setRowSelection(newSelection);
        }
    }, [data, rowSelection]);

    const isAllDataSelected = useMemo(() => {
        return data.length > 0 &&
            Object.keys(rowSelection).length === data.length &&
            data.every((_, idx) => !!rowSelection[idx]);
    }, [data, rowSelection]);

    return (
        <>
            <div className='container d-flex justify-content-end'>
            <TextLink href={route('logout')} method="post" className="btn btn-danger mt-1"
                style={{ }}>
                Log out
            </TextLink>
            </div>
            <div className="container bg-white p-4 mt-3 rounded shadow">
                {showUploadModal && (
                    <UploadModal
                        fiscalYear={filterFiscalYear}
                        setShowUploadModal={setShowUploadModal}
                        refetch={refetch}
                        showUploadModal={showUploadModal}
                    />
                )}

                {downloadModalShowed && (
                    <DownloadProgressModal
                        progress={0}
                        setState={setDownloadModalShowed}
                        state={downloadModalShowed}
                        statusText={status}
                        downloadId={download_id}
                    />
                )}

                <div className="container mt-4">
                    <div className="d-flex align-items-center gap-2 mb-3">
                        <button className="btn btn-success" onClick={() => setShowUploadModal(prev => !prev)}>
                            Add New Data
                        </button>

                        <div className='d-flex gap-2' style={{ width: "36rem" }}>
                            <SponsorFilter onFilterChange={setFilterValue} />
                            <FiscalYearFilter onFilterChange={setFilterFiscalYear} />
                        </div>

                        <input
                            type="text"
                            id="search"
                            className="form-control w-25 ms-auto"
                            placeholder="Search for sponsors..."
                            onChange={(e) => setSearchQuery(e.target.value)}
                        />
                    </div>

                    <div className="d-flex align-items-center mb-2">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                id="selectAllItems"
                                className="form-check-input me-2"
                                checked={isAllDataSelected}
                                onChange={handleSelectAllData}
                            />
                            <label className="form-check-label" htmlFor="selectAllItems">
                                Select All
                            </label>
                        </div>
                    </div>

                    <SelectionActionModal
                        rowSelection={rowSelection}
                        setRowSelection={setRowSelection}
                        selectedCategory={filterValue}
                        selectedFiscalYear={filterFiscalYear}
                        mutateDownload={mutateDownload}
                        mutateDelete={mutateDelete}
                        childData={data}
                        fiscalYear={filterFiscalYear}
                        setDownloadModalShowed={setDownloadModalShowed}
                    />

                    <div className="table-responsive">
                        <table className="table table-bordered mt-3">
                            <thead className="table-light">
                                <tr>
                                    <th>
                                        <input
                                            type="checkbox"
                                            className="form-check-input"
                                            checked={allRowsSelected}
                                            onChange={toggleSelectAll}
                                        />
                                    </th>
                                    <th onClick={() => toggleSort('child_idn')} style={{ cursor: 'pointer' }}>
                                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                            Child Code
                                            <div style={{
                                                display: 'inline-flex',
                                                flexDirection: 'column',
                                                padding: '4px',
                                                borderRadius: '4px',
                                                backgroundColor: 'white'
                                            }}>
                                                <span style={{ fontSize: '8px', lineHeight: '0.8' }}>▲</span>
                                                <span style={{ fontSize: '8px', lineHeight: '0.8' }}>▼</span>
                                            </div>
                                        </div>
                                    </th>
                                    <th>Sponsor ID</th>
                                    <th>Sponsor Name</th>
                                    <th>Sponsor Category</th>
                                    <th>Fiscal Year</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            {isLoading ? (
                                <tbody>
                                    <tr>
                                        <td colSpan={7} className="text-center py-4">
                                            <div className="spinner-border text-primary" role="status">
                                                <span className="visually-hidden">Loading...</span>
                                            </div>
                                            <p className="mt-2">Loading data...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            ) : data.length === 0 ? (
                                <tbody>
                                    <tr>
                                        <td colSpan={7} className="text-center py-4">
                                            No data available
                                        </td>
                                    </tr>
                                </tbody>
                            ) : (
                                <tbody id="sponsorTable">
                                    {displayData.map((row, localIndex) => {
                                        const globalIndex = currentPage * pageSize + localIndex;
                                        return (
                                            <tr key={globalIndex}>
                                                <td>
                                                    <input
                                                        type="checkbox"
                                                        className="form-check-input"
                                                        checked={!!rowSelection[globalIndex]}
                                                        onChange={() => toggleRowSelection(localIndex)}
                                                    />
                                                </td>
                                                <td>{row.child_idn}</td>
                                                <td>{row.sponsor_id}</td>
                                                <td>{row.sponsor_name}</td>
                                                <td>{row.sponsor_category}</td>
                                                <td>{row.fiscal_year}</td>
                                                <td>
                                                    <div className="d-flex gap-2">
                                                        <button
                                                            className="btn btn-sm btn-primary"
                                                            onClick={() => handleDownload(row)}
                                                        >
                                                            Download
                                                        </button>
                                                        <button
                                                            className="btn btn-sm btn-danger"
                                                            onClick={() => handleDelete(row)}
                                                        >
                                                            Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            )}
                        </table>
                    </div>

                    {/* Simple pagination */}
                    <div className="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {Math.min(data.length, 1 + currentPage * pageSize)} to {Math.min(data.length, (currentPage + 1) * pageSize)} of {data.length} entries
                        </div>
                        <div className="btn-group">
                            <button
                                className="btn btn-outline-secondary"
                                onClick={() => setCurrentPage(p => Math.max(0, p - 1))}
                                disabled={currentPage === 0}
                            >
                                Previous
                            </button>
                            {[...Array(Math.min(5, totalPages))].map((_, idx) => {
                                const pageNum = currentPage <= 2
                                    ? idx
                                    : currentPage >= totalPages - 3
                                        ? totalPages - 5 + idx
                                        : currentPage - 2 + idx;

                                if (pageNum < totalPages && pageNum >= 0) {
                                    return (
                                        <button
                                            key={idx}
                                            className={`btn ${currentPage === pageNum ? 'btn-primary' : 'btn-outline-secondary'}`}
                                            onClick={() => setCurrentPage(pageNum)}
                                        >
                                            {pageNum + 1}
                                        </button>
                                    );
                                }
                                return null;
                            })}
                            <button
                                className="btn btn-outline-secondary"
                                onClick={() => setCurrentPage(p => Math.min(totalPages - 1, p + 1))}
                                disabled={currentPage >= totalPages - 1}
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}