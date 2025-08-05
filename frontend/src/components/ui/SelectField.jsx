import React from 'react';

function SelectField({ label, name, value, onChange, options, error }) {
    return (
        <div className="form-group">
            <label>{label}</label>
            <select
                name={name}
                className={`form-control ${error ? 'is-invalid' : ''}`}
                value={value}
                onChange={onChange}
                required
            >
                <option value="">Select {label}</option>
                {options.map((option) => (
                    <option key={option.id} value={option.id}>
                        {option.name}
                    </option>
                ))}
            </select>
            {error && <div className="invalid-feedback">{error}</div>}
        </div>
    );
}

export default SelectField;
