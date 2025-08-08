import React from 'react';

function formatAmount(cents) {
    if (typeof cents !== 'number') return '$0.00';
    return `$${(cents / 100).toFixed(2)}`;
}

function TransactionTable({ transactions = [], loading = false }) {
    return (
        <div className="mt-4">
            <h5>Wallet Transactions</h5>
            {loading ? (
                <p>Loading...</p>
            ) : transactions.length > 0 ? (
                <ul className="list-group">
                    {transactions.map((tx) => (
                        <li
                            key={tx.id}
                            className="list-group-item d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <span
                                    className={`badge badge-${tx.type === 'credit' ? 'success' : 'danger'} mr-2`}
                                >
                                    {tx.type?.toUpperCase()}
                                </span>
                                {tx.description}
                                <br />
                                <small className="text-muted">
                                    {tx?.created_by ? `Created by: ${tx.created_by}` : 'System generated'}
                                </small>
                            </div>
                            <strong>{formatAmount(tx.amount)}</strong>
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
