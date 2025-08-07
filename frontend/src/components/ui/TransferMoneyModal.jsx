import React, { useState, useEffect } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';

function TransferMoneyModal({ show, onClose, onSubmit }) {
    const [users, setUsers] = useState([]);
    const [selectedEmail, setSelectedEmail] = useState('');
    const [amount, setAmount] = useState('');
    const [error, setError] = useState('');

    useEffect(() => {
        if (show) {
            axios.get('/api/users/list')
                .then(res => setUsers(res.data))
                .catch(() => setUsers([]));

            setSelectedEmail('');
            setAmount('');
            setError('');
        }
    }, [show]);

    if (!show) return null;

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');

        try {
            // ✅ 1. Fetch CSRF cookie (Laravel Sanctum)
            await axios.get('/sanctum/csrf-cookie');

            // ✅ 2. Set CSRF token in Axios headers
            const csrfToken = Cookies.get('XSRF-TOKEN');
            if (csrfToken) {
                axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(csrfToken);
            }

            // ✅ 3. Proceed with transfer
            await onSubmit({
                to_user_email: selectedEmail,
                amount: parseFloat(amount) * 100,
            });

            onClose();
        } catch (err) {
            setError(err.response?.data?.message || 'Transfer failed.');
        }
    };

    return (
        <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
            <div className="modal-dialog">
                <form className="modal-content" onSubmit={handleSubmit}>
                    <div className="modal-header">
                        <h5 className="modal-title">Transfer Money</h5>
                        <button type="button" className="close" onClick={onClose}><span>&times;</span></button>
                    </div>
                    <div className="modal-body">
                        {error && <div className="alert alert-danger">{error}</div>}

                        <div className="form-group">
                            <label>Recipient</label>
                            <select
                                className="form-control"
                                value={selectedEmail}
                                onChange={(e) => setSelectedEmail(e.target.value)}
                                required
                            >
                                <option value="">Select a user</option>
                                {users.map(user => (
                                    <option key={user.id} value={user.email}>
                                        {user.name} ({user.email})
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="form-group mt-2">
                            <label>Amount ($)</label>
                            <input type="number" min="0.01" step="0.01" className="form-control"
                                value={amount} onChange={(e) => setAmount(e.target.value)} required />
                        </div>
                    </div>

                    <div className="modal-footer">
                        <button className="btn btn-secondary" onClick={onClose}>Cancel</button>
                        <button className="btn btn-primary" type="submit">Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default TransferMoneyModal;
