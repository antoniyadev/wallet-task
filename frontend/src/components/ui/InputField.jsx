import React from 'react';

function InputField({ label, name, type = 'text', value, onChange, error }) {
    return (
        <div className="form-group">
            <label>{label}</label>
            <input
                name={name}
                type={type}
                className={`form-control ${error ? 'is-invalid' : ''}`}
                value={value}
                onChange={onChange}
                required
            />
            {error && <div className="invalid-feedback">{error}</div>}
        </div>
    );
}

export default InputField;
