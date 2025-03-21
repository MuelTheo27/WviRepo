/* eslint-disable @typescript-eslint/no-unused-vars */
import React, { useEffect, useState, useRef, useCallback } from 'react';
import axios from 'axios';

interface DownloadProgressModalProps {
  state: boolean;
  setState: (value: boolean) => void;
  progress?: number;
  statusText?: string;
  downloadId?: string;
}

const DownloadProgressModal: React.FC<DownloadProgressModalProps> = ({
  state,
  setState,
  progress = 0,
  statusText = "",
  downloadId
}) => {
  // Local state for progress and status
  const [currentProgress, setCurrentProgress] = useState(progress);
  const [currentStatusText, setCurrentStatusText] = useState(statusText);
  const [isPolling, setIsPolling] = useState(false);
  const intervalRef = useRef<number | null>(null);
  const completedRef = useRef(false); 

  const clearProgressCache = useCallback(async () => {
    if (downloadId) {
      try {
        await axios.get(`/api/download/clear-progress?download_id=${downloadId}`);
        console.log('Download progress cache cleared');
      } catch (error) {
        console.error('Error clearing download progress cache:', error);
      }
    }
  }, [downloadId]);
  
  const fetchProgress = async () => {
    if (!downloadId || completedRef.current) return;

    try {
      const response = await axios.get(`/api/download/progress?download_id=${downloadId}`);
      const data = response.data;
      
      setCurrentProgress(prev => {
        const newProgress = data.total > 0 
          ? Math.round((data.progress / data.total) * 100) 
          : 0;

        if (newProgress >= 100) {
        
          completedRef.current = true;
          
          if (intervalRef.current) {
            clearInterval(intervalRef.current);
            intervalRef.current = null;
          }
          clearProgressCache();
          setIsPolling(false);
          setCurrentStatusText('Download complete!');
          return 100;
        }

        return newProgress;
      });
    } catch (error) {
      console.error('Error fetching download progress:', error);
      setCurrentStatusText('Error checking progress');
      setIsPolling(false);
      if (intervalRef.current) {
        clearInterval(intervalRef.current);
        intervalRef.current = null;
      }
    }
  };

  // Set up polling interval when modal is open
  useEffect(() => {
    if (state && downloadId && !completedRef.current) {
      setIsPolling(true);

      fetchProgress();

      intervalRef.current = window.setInterval(fetchProgress, 1000) as unknown as number;

      return () => {
        if (intervalRef.current) {
          clearInterval(intervalRef.current);
          intervalRef.current = null;
        }
        if (!state) setIsPolling(false);
      };
    }

    return undefined;
  }, [state, downloadId]); 

  useEffect(() => {
    if (state && !completedRef.current) {
      setCurrentProgress(progress);
      setCurrentStatusText(statusText);
    }
  }, [state, progress, statusText]);

  // Reset completed flag when modal is closed
  useEffect(() => {
    if (!state) {
      completedRef.current = false;
    }
  }, [state]);


  return (
    <div
      className="modal fade show"
      id="downloadProgressModal"
      tabIndex={-1}
      aria-labelledby="downloadProgressModalLabel"
      aria-hidden="true"
      style={{
        display: 'block',
        backgroundColor: 'rgba(0,0,0,0.5)'
      }}
    >
      <div className="modal-dialog modal-dialog-centered">
        <div className="modal-content">
          <div className="modal-header">
            <h5 className="modal-title" id="downloadProgressModalLabel">Downloading</h5>
            <button
              type="button"
              className="btn-close"
              onClick={() => setState(false)}
              aria-label="Close"
              disabled={isPolling && currentProgress < 100}
            ></button>
          </div>

          <div className="modal-body">
            <div className="progress" style={{ height: '20px' }}>
              <div
                className={`progress-bar progress-bar-striped 
      ${currentProgress < 100 && !currentStatusText.toLowerCase().includes('error') ? 'progress-bar-animated' : ''} 
      ${currentStatusText.toLowerCase().includes('error') ? 'bg-danger' :
                    currentProgress === 100 ? 'bg-success' : 'bg-info'}`}
                role="progressbar"
                style={{ width: `${currentProgress}%` }}
                aria-valuenow={currentProgress}
                aria-valuemin={0}
                aria-valuemax={100}
              >
                {currentProgress > 0 ? `${currentProgress}%` : ''}
              </div>
            </div>
            {statusText === "success" ? <p className="mt-3" id="statusText">Download complete!</p> : null}
            {statusText === "error" ? <p className="mt-3" id="statusText">Error downloading file</p> : null}
          </div>

          <div className="modal-footer">
           
            <button
              type="button"
              className={`btn ${currentProgress === 100 ? 'btn-success' : 'btn-secondary'}`}
              onClick={() => setState(false)}
              disabled={isPolling && currentProgress < 100}
            >
              Done
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DownloadProgressModal;