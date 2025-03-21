/* eslint-disable @typescript-eslint/no-unused-expressions */
/* eslint-disable @typescript-eslint/no-explicit-any */
/* eslint-disable @typescript-eslint/no-unused-vars */
import { useState, useEffect, useCallback, useRef } from 'react';
import Dropzone, { DropzoneFile } from 'dropzone';
import 'dropzone/dist/dropzone.css';
import { UploadStatus } from '@/types/table';
export function useUpload(fiscalYear: number, setIsProcessing: (state: boolean) => void, refetch: () => void, setStatus : React.Dispatch<React.SetStateAction<UploadStatus[]>>, setErrors : React.Dispatch<React.SetStateAction<string[]>> ) {
    const dropzoneInstance = useRef<Dropzone | null>(null);
    const [file, setFiles] = useState<DropzoneFile[]>([]);
    const setIsProcessingRef = useRef(setIsProcessing);
    const isUploadingRef = useRef(false); 
    const fiscalYearRef = useRef(fiscalYear);

    useEffect(() => {
      setIsProcessingRef.current = setIsProcessing;
      fiscalYearRef.current = fiscalYear;
    }, [setIsProcessing, refetch, fiscalYear]);
  
    
    const dropzoneRef = useCallback((element: HTMLElement | null) => {
        if (element && !dropzoneInstance.current) {
            const instance = new Dropzone(element as HTMLElement, {
                url: route('upload.xlsx'),
                paramName: 'file',
                maxFiles: 5,
                acceptedFiles: '.xlsx',
                createImageThumbnails: false,
                previewsContainer: false,
                addRemoveLinks: false,
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads : 5,

                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                
                init: function() {
                    this.on("sending", function(file, xhr) {
                        isUploadingRef.current = true;
                    });
                    
                    this.on("queuecomplete", function() {
                        setTimeout(() => {
                            isUploadingRef.current = false;
                        }, 1000); 
                    });
                    
                    this.on("error", function() {
                        isUploadingRef.current = false;
                    });
                  
                },

                addedfiles: (files) => {
                    setFiles(prevFiles => [...prevFiles, ...files]);
                   
                },
                
                sendingmultiple(files, xhr, formData) {
            
                    formData.append('fiscalYear', fiscalYearRef.current.toString());
                    formData.append('fileId', JSON.stringify(files.map(file => file.upload?.uuid)));
                    
                },

                errormultiple(files, message, xhr) {
                    setErrors(files.map(file => file.upload!.uuid));
                },

                successmultiple(files, responseText) {
                  const responseArray: UploadStatus[] = Object.entries(responseText as unknown as Record<string, any>).map(([id, data]) => ({
                    id,
                    data: {
                      success: data.success ?? 0,
                      ...data
                    }
                  }));
                  console.log(responseArray)
                  setStatus(prev => [...prev, ...responseArray]);
                  
                  refetch();
                },
            });

            dropzoneInstance.current = instance;
        }
        
        return () => {
            if (dropzoneInstance.current && !isUploadingRef.current) {
                dropzoneInstance.current.destroy();
                dropzoneInstance.current = null;
            }
        };
    }, []);
    
    const processQueue = useCallback(() => {
        if (dropzoneInstance.current) {
            try {
                isUploadingRef.current = true;
                dropzoneInstance.current.processQueue();
            } catch (err) {
                console.error("Error processing queue:", err);
                isUploadingRef.current = false;
            }
        }
    }, []);

    const removeFile = useCallback((file: File) => {
        if (dropzoneInstance.current) {
            const dzFile = dropzoneInstance.current.files.find(f => f.name === file.name);
            if (dzFile) {
                dropzoneInstance.current.removeFile(dzFile);
                setFiles(prevFiles => prevFiles.filter(f => f.name !== file.name));
            }
        }
    }, []);

    return { dropzoneRef, file, processQueue, removeFile };
}
