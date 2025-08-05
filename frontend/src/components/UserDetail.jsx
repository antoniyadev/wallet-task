import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { useParams, Link } from 'react-router-dom';

function UserDetail() {
    const { id } = useParams();
    const [user, setUser] = useState(null);

    useEffect(() => {
        axios.get(`/api/admin/users/${id}`)
            .then(res => setUser(res.data))
            .catch(err => console.error('Failed to load user:', err));
    }, [id]);

    if (!user) return <p>Loading...</p>;

    return (
        <div className="container mt-4">
            <Link to="/admin" className="btn btn-sm btn-secondary mb-3">← Back</Link>
            <h4>{user.name}</h4>
            <p>Email: {user.email}</p>
            <p>Wallet: ${(user.amount / 100).toFixed(2)}</p>

            <h5 className="mt-4">Transactions</h5>
            {user.transactions?.length > 0 ? (
                <ul className="list-group">
                    {user.transactions.map(tx => (
                        <li key={tx.id} className="list-group-item d-flex justify-content-between">
                            <span>{tx.type.toUpperCase()} - {tx.description}</span>
                            <strong>${(tx.amount / 100).toFixed(2)}</strong>
                        </li>
                    ))}
                </ul>
            ) : (
                <p>No transactions yet.</p>
            )}
        </div>
    );
}

export default UserDetail;
