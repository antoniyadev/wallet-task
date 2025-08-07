import React, { useEffect, useState } from 'react';
import axios from 'axios';

function TransactionTable({ transactions }) {
    const [setTransactions] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchTransactions();
    }, []);

    const fetchTransactions = async () => {
        try {
            const res = await axios.get('/api/transactions');
            setTransactions(res.data);
        } catch (err) {
            console.error('Failed to fetch transactions:', err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="mt-4">
            <h5>Wallet Transactions</h5>
            {loading ? (
                <p>Loading...</p>
            ) : transactions.length > 0 ? (
                <ul className="list-group">
                    {transactions.map(tx => (
                        <li key={tx.id} className="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span className={`badge badge-${tx.type === 'credit' ? 'success' : 'danger'} mr-2`}>
                                    {tx.type.toUpperCase()}
                                </span>
                                {tx.description}
                                <br />
                                <small className="text-muted">
                                    {tx.creator?.email ? `Created by: ${tx.creator.email}` : 'System generated'}
                                </small>
                            </div>
                            <strong>${(tx.amount / 100).toFixed(2)}</strong>
                        </li>
                    ))}
                </ul>
            ) : (
                <p>No transactions found.</p>
            )}
        </div>
    );
}

export default TransactionTable;
