/* eslint-disable @typescript-eslint/no-unused-vars */
import React, {Dispatch, SetStateAction, useEffect} from 'react'
import useDownload from '../../hooks/use-download';
import { ChildData } from '@/types/table';

type SelectionActionModalProps = {
    rowSelection: Record<number, boolean>;
    setRowSelection: Dispatch<SetStateAction<Record<number, boolean>>>;
    selectedCategory: string;
    selectedFiscalYear: string;
    setDownloadModalShowed: (bool : boolean) => void;
    fiscalYear : string;
    childData: ChildData[];
    mutateDownload : (args : {child_idn : string[], fiscal_year : string}) => void;
    mutateDelete : (args : {deleteList : { child_idn: string, fiscal_year : string }[]}) => void;
};
export default function SelectionActionModal({rowSelection, setRowSelection, selectedCategory, selectedFiscalYear, setDownloadModalShowed, childData, fiscalYear, mutateDownload, mutateDelete} : SelectionActionModalProps) {
    const [showSelectionActionModal, setShowSelectionActionModal] = React.useState(false);
    function handleBulkDownload(){
        mutateDownload({
            child_idn: childData
            .filter((_, index) => rowSelection[index])
            .map((item) => 
                item.child_idn,
            ) ,
            fiscal_year: selectedFiscalYear,
            });

     
        setDownloadModalShowed(true);
    }

    function handleBulkDelete(){
        mutateDelete({
            deleteList : childData
            .filter((_, index) => rowSelection[index])
            .map((item) => ({
                child_idn: item.child_idn,
                fiscal_year: item.fiscal_year
            }))
        });

        setRowSelection({});
    }
  
    useEffect(()=>{
        console.log(rowSelection)
    }, [rowSelection])
    return (
        <>
        { Object.keys(rowSelection).length  < 2 ? null :
            <div className="alert alert-danger d-flex align-items-center justify-content-between mb-3">
                <span><span id="selectedCount">{Object.keys(rowSelection).length}</span> Selected</span>
                <div className='d-flex gap-3'>
                    { selectedCategory !== 'a' && selectedFiscalYear !== '0' ? 
                    <button id="downloadSelected" className="btn btn-sm btn-primary" onClick={() => handleBulkDownload()}> Download</button>
                    : null }                   
                    <button id="deleteSelected" className="btn btn-sm btn-danger" onClick={() =>  handleBulkDelete()}> Delete</button>
                </div>
            </div>
                }
        </>
    )
}
