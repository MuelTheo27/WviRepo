/* eslint-disable @typescript-eslint/no-unused-vars */
import { useUpload } from "@/hooks/use-upload";
import React, { startTransition, useDebugValue, useEffect, useState } from "react";
import 'dropzone/dist/dropzone.css';
import axios from "axios";
import { DropzoneFile } from "dropzone";
import Modal from 'react-bootstrap/esm/Modal';
import { UploadStatus } from "@/types/table";
const FiscalYearDropdown = ({ setYear, setMonth }: { setYear: (value: number) => void, setMonth: (value: number) => void }) => {
    const years = Array.from({ length: 10 }, (_, i) => ({
        value: new Date().getFullYear() - i,
        label: (new Date().getFullYear() - i).toString(),
    }));

    const months = [
        { value: 1, label: "January" },
        { value: 2, label: "February" },
        { value: 3, label: "March" },
        { value: 4, label: "April" },
        { value: 5, label: "May" },
        { value: 6, label: "June" },
        { value: 7, label: "July" },
        { value: 8, label: "August" },
        { value: 9, label: "September" },
        { value: 10, label: "October" },
        { value: 11, label: "November" },
        { value: 12, label: "December" },
    ];

    return (
        <div className="row">
            <div className="col-md-6">
                <label className="form-label">Year</label>
                <select className="form-select" onChange={(e) => setYear(parseInt(e.target.value))}>
                    {years.map((option) => (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    ))}
                </select>
            </div>
            <div className="col-md-6">
                <label className="form-label">Month</label>
                <select className="form-select" onChange={(e) => setMonth(parseInt(e.target.value))}>
                    {months.map((option) => (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    ))}
                </select>
            </div>
        </div>
    );
}

function calculateFiscalYear(month : number, year : number) : number{
    if (month >= 9) {
        return year + 1;
    }
    return year;
}


export function UploadModal({ fiscalYear, setShowUploadModal, refetch, showUploadModal }: { fiscalYear: string, setShowUploadModal: (state: boolean) => void, refetch: () => void, showUploadModal: boolean }) {

    const [month, setMonth] = useState(1);
    const [year, setYear] = useState(2025);
    const [status, setStatus] = useState<UploadStatus[]>([]);
    const [errors, setErrors] = useState<string[]>([]);
    const [intervals, setIntervals] = useState<{uuid : string, interval : NodeJS.Timeout}[]>([]);
    const [isProcessing, setIsProcessing] = useState(false);
    const [progress, setProgress] = useState<Record<string, number>>({});
    const { dropzoneRef, file, removeFile, processQueue } = useUpload(calculateFiscalYear(month, year), setIsProcessing, refetch, setStatus, setErrors);
    const [csrfToken, setCsrfToken] = useState('');

    const getListBackgroundColor = (id : string) => {
        const isSuccess = status.find(value => value.id === id)?.data.message.length === 0 ;
        if(!isSuccess && progress[id] === 100){
            return 'bg-danger'
        }
        else if(isSuccess && progress[id] === 100){
            return 'bg-success'
        }

        return '';
    
    }

    const alertType = [
        "alert alert-danger mt-2",
        "alert alert-success mt-2",
        "alert alert-info mt-2",
        ""
    ];

    const alertMessage = [
        "Upload Failed",
        "Upload Partially Successful",
        "Upload Successful",
        ""
    ];

    const getStatusMessage = (id : string) => {
        const fileStatus = status.find(value => value.id === id);

        if(fileStatus !== undefined){
            if(errors.includes(id) || fileStatus.data.success === 0){
                return 0;
            // return <div className="alert alert-danger mt-2" role="alert">
            //     Error uploading Excel File </div>
            }
            else if(fileStatus.data.message.length > 0 && fileStatus.data.success >= 1){       
                return 1;
            }
            else if(fileStatus.data.message.length === 0){
                return 2;
            }

        }
        return 3;
    }
    
    // useEffect(()=>{
 
    //        console.log(status)
        
    // }, [status])

    const getProgress = (file: DropzoneFile) => {
        const intervalId = setInterval(async () => {
            if (!file.upload?.uuid) return;

            try {
                const res = await axios.get(`api/upload/progress?upload_id=${file.upload.uuid}`);
                const newProgress = res.data.total > 0
                    ? Math.round((res.data.progress / res.data.total) * 100)
                    : 0;

                setProgress((prev) => ({
                    ...prev,
                    [file.upload!.uuid]: newProgress,
                }));
                if (newProgress >= 100) {
                    setIntervals((prev) => prev.filter((id) => id.interval !== intervalId));

                    clearInterval(intervalId);
                }
            } catch (error) {
                console.error("Error fetchings:", error);
                setIntervals((prev) => prev.filter((id) => id.interval !== intervalId));
                clearInterval(intervalId);
            }
        }, 1000);

        return intervalId;
    };

    useEffect(() => {
        if (isProcessing) {
            const newIntervals = file.map((fileItem) => ({
              uuid: fileItem.upload!.uuid,
              interval: getProgress(fileItem)
            }));
          
            setIntervals((prev) => [...prev, ...newIntervals]);
          
            return () => {
              newIntervals.forEach(({ interval }) => clearInterval(interval));
            };
          }
          
    }, [isProcessing, file]);

    useEffect(() => {
    
        errors.forEach((error) => {
          const matchingInterval = intervals.find((interval) => interval.uuid === error);
          if (matchingInterval) {
            clearInterval(matchingInterval.interval);
          }
        });
      
        setIntervals((prev) => prev.filter((interval) => !errors.some((error) => error === interval.uuid)));
      }, [errors]);
      

    const handleClose = () => {
        if (!isProcessing || intervals.length === 0) {
            setShowUploadModal(false);
            if (isProcessing) {
                setIsProcessing(false);
            }
        }
    };

    return (
        <Modal show={showUploadModal} onHide={handleClose}>
            <div className="modal-header">
                <h5 className="modal-title" id="modalTitle">
                    {isProcessing ? "Upload Progress" : "Upload Your Files"}
                </h5>
                <button 
                    type="button" 
                    className="btn-close" 
                    onClick={handleClose} 
                    aria-label="Close"
                ></button>
            </div>
            
            <div className="modal-body">
                {isProcessing ? (
                    <>
                        {file.map((file, index) => (
                            <li className={`list-group ${getStatusMessage(file.upload!.uuid) === 3 ? "mb-5" : "mb-3"}`} style={{
                            } } key={index}>
                                <div className={`w-100 ${alertType[getStatusMessage(file.upload!.uuid)]} d-flex flex-column gap-2`}>
                                    <span>{file.name}</span>
                                    <div className={`progress w-100}`} style={{ height: "20px" }}>
                                        <div
                                            className={`progress-bar ${getListBackgroundColor(file.upload!.uuid)}`}
                                            role="progressbar"
                                            style={{ width: `${progress[file.upload?.uuid || ''] || 0}%` }}
                                            aria-valuenow={progress[file.upload?.uuid || ''] || 0}
                                            aria-valuemin={0}
                                            aria-valuemax={100}
                                        >
                                            {progress[file.upload?.uuid || ''] || 0}%
                                        </div>
                                    </div>
                                {alertMessage[getStatusMessage(file.upload!.uuid)]}
                                </div>
                            </li>
                        ))}
                    </>
                ) : (
                    <>
                        <div className="row mb-3">
                            <div className="col-md-6 w-100">
                                <FiscalYearDropdown setYear={setYear} setMonth={setMonth} />
                            </div>
                        </div>
                        <input
                            type="hidden"
                            name="_token"
                            value={csrfToken}
                            ref={(input) => {
                                if (input && !csrfToken) {
                                    setCsrfToken(document.querySelector('input[name="_token"]')?.getAttribute('value') || "");
                                }
                            }}
                        />
                        <div className="dropzone" style={{
                            border: "2px dashed #ccc",
                            background: "#f9f9f9",
                            padding: "20px",
                            textAlign: "center",
                            cursor: "pointer",
                            position: "relative",
                            marginBottom: "15px"
                        }} ref={dropzoneRef}>
                            <div className="dz-message">
                                <p>Drag and drop files here</p>
                            </div>
                        </div>
                        {file.map((file, index) =>
                            <li className="success-file d-flex flex-column p-2" style={{
                                listStyle: "none",
                                padding: "8px",
                                marginBottom: "5px",
                                borderRadius: "5px",
                                display: "flex",
                                justifyContent: "space-between",
                                alignItems: "center",
                                fontSize: "14px"
                            }}
                                key={index}>
                                <div className="d-flex justify-content-between align-items-center w-100">
                                    <div>{file.name}</div>
                                    <button className="remove-file" style={{
                                        backgroundColor: "#dc3545",
                                        color: "white",
                                        border: "none",
                                        padding: "5px 10px",
                                        borderRadius: "5px",
                                        cursor: "pointer"
                                    }}
                                        onClick={() => removeFile(file)}>Remove</button>
                                </div>
                            </li>
                        )}
                    </>
                )}
            </div>
            
            <div className="modal-footer">
                {!isProcessing && file.length > 0 && (
                    <button 
                        type="button" 
                        className="btn btn-primary" 
                        onClick={() => { 
                            setIsProcessing(true);
                            processQueue();
                        }}
                    >
                        Upload
                    </button>
                )}
                {isProcessing && intervals.length === 0 && (
                    <button 
                        type="button" 
                        className="btn btn-success" 
                        onClick={handleClose}
                    >
                        Done
                    </button>
                )}
            </div>
        </Modal>
    );
}