import { useQuery } from '@tanstack/react-query';
import axios from 'axios';
import { ChildData } from '@/types/table';

interface TableDataProps {
  fiscalYear?: string;
  sponsorCategory?: string;
  searchQuery?: string;
}



export default function useFetchTableData({ fiscalYear, sponsorCategory, searchQuery }: TableDataProps) {

  const queryParams = {
    ...(fiscalYear ? { fiscalYear } : {}),
    ...(sponsorCategory ? { sponsorCategory } : {}),
    ...(searchQuery ? { searchQuery } : {})
  };

  const queryKey = ['tableData', fiscalYear, sponsorCategory, searchQuery];

  return useQuery({
    queryKey,
    queryFn: async () => {
      const { data } = await axios.get<ChildData[]>('api/results', {
        params: queryParams
      });
      console.log(data)
      return data;
    },
    refetchOnWindowFocus: false,
    enabled: true, 
    refetchInterval: false,
    retry : false
  });
}