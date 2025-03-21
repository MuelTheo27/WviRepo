import React, { useState } from 'react';

type FiscalYearFilterProps = {
  onFilterChange?: (value: string) => void;
  className?: string;
  defaultValue?: string;
};

export function FiscalYearFilter({ 
  onFilterChange, 
  className = '', 
}: FiscalYearFilterProps) {
  const [selectedValue, setSelectedValue] = useState<string>('0');
  
  // Generate fiscal years (current year - 5 to current year + 2)
  const generateFiscalYears = () => {
    const currentYear = new Date().getFullYear();
    const years = [];
    
    years.push({ value: 0, label: 'No Filter' });
    
    for (let i = 0; i < 7; ++i){
        years.push({value : currentYear - i, label : `${currentYear - i}`});
    }
    
    return years;
  };
  
  const fiscalYears = generateFiscalYears();
  
  const handleChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const value = e.target.value;
    setSelectedValue(value);
    if (onFilterChange) {
      onFilterChange(value);
    }
  };
  
  return (
    <select 
      id="fiscalYearFilter" 
      className={`form-select ${className}`}
      value={selectedValue}
      onChange={handleChange}
      style={{ width: '10rem' }}
    >
      {fiscalYears.map((year, index) => (
        <option key={index} value={year.value}>
          {year.label}
        </option>
      ))}
    </select>
  );
}