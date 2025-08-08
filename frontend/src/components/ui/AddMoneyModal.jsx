import React, { useEffect, useState } from 'react';

function AddMoneyModal({
    show,
    onClose,
    onSubmit,
    submitLabel = 'Submit',
    showDescription = false,
    showType = false,
    showTitle = false,
    titleLabel = 'Title', // label override
    onSuccess,
}) {
    const [amount, setAmount] = useState('');
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [type, setType] = useState('credit');
    const [errors, setErrors] = useState({});
    const [formError, setFormError] = useState('');
    const [success, setSuccess] = useState('');

    useEffect(() => {
        if (show) {
            setAmount('');
            setTitle('');
            setDescription('');
            setType('credit');
            setErrors({});
            setFormError('');
            setSuccess('');
        }
    }, [show]);

    if (!show) return null;

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors({});
        setFormError('');

        const numericAmount = parseFloat(amount);
        if (isNaN(numericAmount) || numericAmount === 0) {
            setErrors((prev) => ({ ...prev, amount: 'Please enter a non‑zero amount.' }));
            return;
        }

        if (showDescription && !description.trim()) {
            setErrors((prev) => ({ ...prev, description: 'Description is required.' }));
            return;
        }

        const payload = {
            amount: Math.round(Math.abs(numericAmount) * 100),
        };

        if (showType) {
            payload.type = numericAmount < 0 ? 'debit' : 'credit';
        }
        if (showTitle) payload.title = title;
        if (showDescription) payload.description = description;

        try {
            await onSubmit(payload);
            onSuccess?.('✅ Money added successfully');
            onClose();
        } catch (err) {
            const res = err.response;
            if (res?.status === 422 && res?.data?.errors) {
                setErrors(res.data.errors);
            } else {
                setFormError(res?.data?.message || 'Transaction failed.');
            }
        }
    };

    const invalidClass = (name) => (errors[name] ? 'is-invalid' : '');

    return (
        <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
            <div className="modal-dialog">
                <form className="modal-content" onSubmit={handleSubmit} noValidate>
                    <div className="modal-header">
                        <h5 className="modal-title">Add Money</h5>
                        <button type="button" className="close" onClick={onClose}>
                            <span>&times;</span>
                        </button>
                    </div>

                    <div className="modal-body">
                        {formError && <div className="alert alert-danger">{formError}</div>}
                        {success && <div className="alert alert-success">{success}</div>}

                        <div className="form-group">
                            <label>Amount ($)</label>
                            <input
                                type="number"
                                className={`form-control ${invalidClass('amount')}`}
                                value={amount}
                                onChange={(e) => setAmount(e.target.value)}
                                required
                            />
                            {errors.amount && <div className="invalid-feedback">{errors.amount}</div>}
                            {showType && (
                                <small className="form-text text-muted">
                                    Use a negative number to remove funds (debit).
                                </small>
                            )}
                        </div>

                        {showTitle && (
                            <div className="form-group mt-2">
                                <label>{titleLabel}</label>
                                <input
                                    className={`form-control ${invalidClass('title')}`}
                                    value={title}
                                    onChange={(e) => setTitle(e.target.value)}
                                    required
                                />
                                {errors.title && <div className="invalid-feedback">{errors.title}</div>}
                            </div>
                        )}

                        {showDescription && (
                            <div className="form-group mt-2">
                                <label>Description</label>
                                <input
                                    className={`form-control ${invalidClass('description')}`}
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                    required
                                />
                                {errors.description && (
                                    <div className="invalid-feedback">{errors.description}</div>
                                )}
                            </div>
                        )}
                    </div>

                    <div className="modal-footer">
                        <button type="button" className="btn btn-secondary" onClick={onClose}>
                            Cancel
                        </button>
                        <button className="btn btn-success" type="submit">
                            {submitLabel}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default AddMoneyModal;
