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
}) {
    const [amount, setAmount] = useState('');
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [type, setType] = useState('credit');
    const [error, setError] = useState('');

    useEffect(() => {
        if (show) {
            setAmount('');
            setTitle('');
            setDescription('');
            setType('credit');
            setError('');
        }
    }, [show]);

    if (!show) return null;

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');

        const numericAmount = parseFloat(amount);
        if (isNaN(numericAmount) || numericAmount === 0) {
            setError('Please enter a non-zero amount.');
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
            onClose();
        } catch (err) {
            setError(err.response?.data?.message || 'Transaction failed.');
        }
    };

    return (
        <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
            <div className="modal-dialog">
                <form className="modal-content" onSubmit={handleSubmit}>
                    <div className="modal-header">
                        <h5 className="modal-title">Add Money</h5>
                        <button type="button" className="close" onClick={onClose}>
                            <span>&times;</span>
                        </button>
                    </div>

                    <div className="modal-body">
                        {error && <div className="alert alert-danger">{error}</div>}

                        <div className="form-group">
                            <label>Amount ($)</label>
                            <input
                                type="number"
                                className="form-control"
                                value={amount}
                                onChange={(e) => setAmount(e.target.value)}
                                required
                            />
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
                                    className="form-control"
                                    value={title}
                                    onChange={(e) => setTitle(e.target.value)}
                                    required
                                />
                            </div>
                        )}

                        {showDescription && (
                            <div className="form-group mt-2">
                                <label>Description</label>
                                <input
                                    className="form-control"
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                />
                            </div>
                        )}
                    </div>

                    <div className="modal-footer">
                        <button className="btn btn-secondary" onClick={onClose}>Cancel</button>
                        <button className="btn btn-success" type="submit">{submitLabel}</button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default AddMoneyModal;
