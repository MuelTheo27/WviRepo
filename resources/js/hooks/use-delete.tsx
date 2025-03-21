import { useMutation, useQueryClient } from '@tanstack/react-query';
import axios from 'axios';


type DeleteParams = {
  deleteList: {child_idn : string, fiscal_year : string}[];
};

export default function useDelete({refetchData} : {refetchData : () => void}) {
  const queryClient = useQueryClient();
  
 
  return useMutation({
    mutationFn: async (params: DeleteParams) => {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      
      const response = await axios({
        url: 'api/delete',
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        data: params
      });
      
      return response.data;
    },
      
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['sponsors'] });
      refetchData();
    },
    
    onError: (error) => {
      console.error('Delete operation failed:', error);
    }
  });
}