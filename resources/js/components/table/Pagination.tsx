import React from 'react';
import { Table } from '@tanstack/react-table';

interface PaginationProps<T> {
  table: Table<T>;
  data: T[];
}

export default function Pagination<T>({ 
  table, 
  data
}: PaginationProps<T>) {
  if (!data || data.length === 0) {
    return null;
  }

  const currentPageIndex = table.getState().pagination.pageIndex;
  const pageCount = table.getPageCount();
  
  if (pageCount <= 1) {
    return null;
  }

  return (
    <nav aria-label="Table pagination" className="mt-4">
      <div className="">
        <ul className="pagination pagination-md">
          <li className={`page-item ${!table.getCanPreviousPage() ? 'disabled' : ''}`}>
            <button 
              className="page-link" 
              onClick={() => table.setPageIndex(0)}
              disabled={!table.getCanPreviousPage()}
              aria-label="First page"
            >
              <span aria-hidden="true">&laquo;</span>
            </button>
          </li>
          
          <li className={`page-item ${!table.getCanPreviousPage() ? 'disabled' : ''}`}>
            <button 
              className="page-link" 
              onClick={() => table.previousPage()}
              disabled={!table.getCanPreviousPage()}
              aria-label="Previous page"
            >
              <span aria-hidden="true">&lsaquo;</span>
            </button>
          </li>
          
          {Array.from({ length: pageCount }).map((_, pageIndex) => {
            const isCurrentPage = pageIndex === currentPageIndex;
            const isFirstPage = pageIndex === 0;
            const isLastPage = pageIndex === pageCount - 1;
            const isNearCurrentPage = 
              Math.abs(pageIndex - currentPageIndex) <= 1;
            
            const shouldShowPage = 
              pageCount <= 7 || 
              isCurrentPage || 
              isFirstPage || 
              isLastPage || 
              isNearCurrentPage;
            
            const showStartEllipsis = 
              pageIndex === 1 && 
              currentPageIndex > 3 && 
              pageCount > 7;
            
            const showEndEllipsis = 
              pageIndex === pageCount - 2 && 
              currentPageIndex < pageCount - 4 && 
              pageCount > 7;
            
            if (!shouldShowPage && !showStartEllipsis && !showEndEllipsis) {
              return null;
            }
            
            if (showStartEllipsis || showEndEllipsis) {
              return (
                <li key={`ellipsis-${pageIndex}`} className="page-item disabled">
                  <span className="page-link">...</span>
                </li>
              );
            }
           
            return (
              <li key={pageIndex} className={`page-item ${isCurrentPage ? 'active' : ''}`}>
                <button
                  className="page-link"
                  onClick={() => table.setPageIndex(pageIndex)}
                  aria-current={isCurrentPage ? 'page' : undefined}
                  aria-label={`Page ${pageIndex + 1}`}
                >
                  {pageIndex + 1}
                </button>
              </li>
            );
          })}
          
     
          <li className={`page-item ${!table.getCanNextPage() ? 'disabled' : ''}`}>
            <button 
              className="page-link" 
              onClick={() => table.nextPage()}
              disabled={!table.getCanNextPage()}
              aria-label="Next page"
            >
              <span aria-hidden="true">&rsaquo;</span>
            </button>
          </li>

          <li className={`page-item ${!table.getCanNextPage() ? 'disabled' : ''}`}>
            <button 
              className="page-link" 
              onClick={() => table.setPageIndex(pageCount - 1)}
              disabled={!table.getCanNextPage()}
              aria-label="Last page"
            >
              <span aria-hidden="true">&raquo;</span>
            </button>
          </li>
        </ul>
      </div>
    
    </nav>
  );
}