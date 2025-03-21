/* eslint-disable @typescript-eslint/no-unused-vars */
import React, {Dispatch, SetStateAction} from 'react';
import { useMutation } from '@tanstack/react-query';
import axios, { AxiosError } from 'axios'; // Import AxiosError type
import { v4 as uuidv4 } from 'uuid';

type DownloadParams = {
  child_idn?: string[];
  fiscal_year?: string;
};

type DownloadProgress = {
  loaded: number;
  total: number;
  percent: number;
};

export default function useDownload({setState, refetchData} : {setState : Dispatch<SetStateAction<boolean>>, refetchData : () => void}) {
  const [progress, setProgress] = React.useState<DownloadProgress | null>(null);
  const [download_id] = React.useState<string>(() => uuidv4());

  const downloadMutation = useMutation({
    mutationFn: async (params: DownloadParams) => {
      setProgress({ loaded: 0, total: 0, percent: 0 });
      
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      
      try {
        setState(true);

        const response = await axios({
          url: 'api/download',
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          data: {
            ...params,
            download_id : download_id
          },
          responseType: 'blob',
  
        });
        
        const contentDisposition = response.headers['content-disposition'];
        let filename = 'download.zip';
        
        if (contentDisposition) {
          const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
          if (filenameMatch && filenameMatch[1]) {
            filename = filenameMatch[1].replace(/['"]/g, '');
          }
        }
      
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        
        window.URL.revokeObjectURL(url);
        document.body.removeChild(link);
    
        setTimeout(() => setProgress(null), 500);
        
        return { success: true };
      } catch (error: unknown) { 
        setProgress(null);
        if (axios.isAxiosError(error) && error.response?.data instanceof Blob) {
          const blob = error.response.data;
          return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => {
              try {
                const errorResponse = JSON.parse(reader.result as string);
                reject(new Error(errorResponse.message || 'Download failed'));
              } catch {
                reject(new Error('Download failed'));
              }
            };
            reader.onerror = () => reject(new Error('Download failed'));
            reader.readAsText(blob);
          });
        }
        
        if (error instanceof Error) {
          throw error; 
        } else {
          throw new Error('An unexpected error occurred during download');
        }
      }
    },
    
    onMutate: () => {
      return { startTime: Date.now() };
    },
    
    onSuccess: (data, variables, context) => {
      refetchData();
    },
    
    onError: (error, variables, context) => {
      console.error('Download failed:', error);
    },
    
    onSettled: (data, error, variables, context) => {
      console.log('Download process finished (success or failure)');
    }
  });
  
  return {
    ...downloadMutation,
    progress,
    download_id,
    startDownload: downloadMutation.mutate,
    startDownloadAsync: downloadMutation.mutateAsync
  };
}