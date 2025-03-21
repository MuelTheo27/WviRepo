export type ChildData = {
  child_idn: string;
  sponsor_id: string;
  sponsor_name: string;
  sponsor_category: string;
  fiscal_year: string;
};

type UploadStatus = {
  id: string;
  data: {
    success: number;
    message : string[];
  };
};