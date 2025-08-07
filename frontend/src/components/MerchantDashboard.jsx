import React, { useState } from 'react';
import axios from 'axios';
import TransactionTable from './TransactionTable';
import AddMoneyModal from './ui/AddMoneyModal';
import TransferMoneyModal from './ui/TransferMoneyModal';
import FlashMessage from './ui/FlashMessage';
import Cookies from 'js-cookie';
import { useEffect } from 'react';

function MerchantDashboard({ user, fetchUser }) {
    const [showAddModal, setShowAddModal] = useState(false);
    const [showTransferModal, setShowTransferModal] = useState(false);
    const [flashMessage, setFlashMessage] = useState('');
    const [transactions, setTransactions] = useState([]);

    useEffect(() => {
        fetchTransactions();
    }, []);
    const handleSuccess = (message) => {
        setFlashMessage(message);
        setTimeout(() => setFlashMessage(''), 3000);
    };

    const fetchTransactions = async () => {
        try {
            const res = await axios.get('/api/transactions');
            setTransactions(res.data.data);
        } catch (err) {
            console.error('Failed to fetch transactions', err);
        }
    };

    return (
        <div className="container mt-4">
            <h3>🛒 Merchant Dashboard</h3>

            <p className="text-muted">
                💰 Wallet Balance: <strong>${(user.amount / 100).toFixed(2)}</strong>
            </p>

            <FlashMessage show={!!flashMessage} message={flashMessage} />

            <div className="d-flex gap-3 mb-3">
                <button className="btn btn-success mr-2" onClick={() => setShowAddModal(true)}>Add Money</button>
                <button className="btn btn-primary" onClick={() => setShowTransferModal(true)}>Transfer Money</button>
            </div>

            <TransactionTable transactions={transactions} />

            {/* Add Money Modal (uses /api/orders) */}
            <AddMoneyModal
                show={showAddModal}
                onClose={() => setShowAddModal(false)}
                submitLabel="Create Order"
                showDescription={false}
                showType={false}
                showTitle={true}
                onSubmit={async ({ amount, title }) => {
                    await axios.get('/sanctum/csrf-cookie');
                    const csrf = Cookies.get('XSRF-TOKEN');
                    axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(csrf);
                    await axios.post('/api/orders', { amount, title });
                    handleSuccess('✅ Order created (pending payment)');
                }}
            />

            {/* Transfer Money Modal (uses /api/transfer) */}
            <TransferMoneyModal
                show={showTransferModal}
                onClose={() => setShowTransferModal(false)}
                onSubmit={async (data) => {
                    await axios.get('/sanctum/csrf-cookie');
                    const csrf = Cookies.get('XSRF-TOKEN');
                    axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(csrf);

                    await axios.post('/api/transfer', data);

                    // ✅ Reload wallet + transactions
                    await fetchUser();          // <- from props
                    await fetchTransactions();  // <- locally defined
                    handleSuccess('✅ Transfer successful');
                }}
            />

        </div>
    );
}

export default MerchantDashboard;
