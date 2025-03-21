import React, { useState } from 'react';

type SponsorFilterProps = {
  onFilterChange?: (value: string) => void;
  className?: string;
}

export function SponsorFilter({ onFilterChange, className = '' }: SponsorFilterProps) {
  const [selectedValue, setSelectedValue] = useState<string>('a');
  
  const handleChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const value = e.target.value;
    setSelectedValue(value);
    if (onFilterChange) {
      onFilterChange(value);
    }
  };
  
  return (
    <select 
      id="sortBySponsor" 
      className={`form-select ${className}`}
      value={selectedValue}
      onChange={handleChange}
      style={{ width: '10rem' }}
    >
      <option value="a">No Filter</option>
      <option value="b">Mass Sponsor</option>
      <option value="c">Middle Sponsor</option>
      <option value="d">Major Sponsor</option>
      <option value="e">Hardcopy</option>
    </select>
  );
}