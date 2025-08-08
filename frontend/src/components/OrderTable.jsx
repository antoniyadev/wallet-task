import React, { useEffect, useState } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';
import 'bootstrap/dist/js/bootstrap.bundle.min.js'; // for dropdowns

function OrderTable() {
    const [orders, setOrders] = useState([]);
    const [statusUpdating, setStatusUpdating] = useState(null);
    const [orderStatuses, setOrderStatuses] = useState([]);

    const [currentPage, setCurrentPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [total, setTotal] = useState(0);

    useEffect(() => {
        fetchOrderStatuses();
    }, []);

    useEffect(() => {
        fetchOrders(currentPage);
    }, [currentPage]);

    const fetchOrders = async (page = 1, per = 10) => {
        try {
            const res = await axios.get(`/api/admin/orders?page=${page}&per_page=${per}`);
            setOrders(res.data.data);
            setCurrentPage(res.data.current_page);
            setLastPage(res.data.last_page);
            setTotal(res.data.total);
        } catch (err) {
            console.error('Failed to fetch orders:', err);
        }
    };

    const fetchOrderStatuses = async () => {
        try {
            const res = await axios.get('/api/admin/orders/statuses');
            // expected: [{ value, label, color }]
            setOrderStatuses(res.data);
        } catch (err) {
            console.error('Failed to fetch statuses:', err);
        }
    };

    const updateStatus = async (orderId, newStatus) => {
        setStatusUpdating(orderId);
        const csrfToken = Cookies.get('XSRF-TOKEN');

        try {
            await axios.put(
                `/api/admin/orders/${orderId}/status`,
                { status: newStatus },
                { headers: { 'X-XSRF-TOKEN': decodeURIComponent(csrfToken) } }
            );
            await fetchOrders(currentPage); // stay on same page
        } catch (err) {
            console.error('Failed to update order:', err.response?.data || err.message);
        } finally {
            setStatusUpdating(null);
        }
    };

    const getStatusConfig = (statusKey) =>
        orderStatuses.find((s) => s.value === statusKey) || { label: statusKey, color: 'secondary' };

    const renderPagination = () => (
        <div className="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
            <div className="text-muted">
                Showing page {currentPage} of {lastPage} • {total} total
            </div>

            <nav>
                <ul className="pagination mb-0">
                    <li className={`page-item ${currentPage === 1 ? 'disabled' : ''}`}>
                        <button className="page-link" onClick={() => setCurrentPage((p) => p - 1)}>
                            Previous
                        </button>
                    </li>

                    {Array.from({ length: lastPage }, (_, i) => i + 1)
                        .filter((n) => n === 1 || n === lastPage || Math.abs(n - currentPage) <= 2)
                        .reduce((acc, n, _, arr) => {
                            if (acc.length && n - acc[acc.length - 1] > 1) acc.push('ellipsis');
                            acc.push(n);
                            return acc;
                        }, [])
                        .map((n, idx) =>
                            n === 'ellipsis' ? (
                                <li key={`e-${idx}`} className="page-item disabled">
                                    <span className="page-link">…</span>
                                </li>
                            ) : (
                                <li key={n} className={`page-item ${currentPage === n ? 'active' : ''}`}>
                                    <button className="page-link" onClick={() => setCurrentPage(n)}>
                                        {n}
                                    </button>
                                </li>
                            )
                        )}

                    <li className={`page-item ${currentPage === lastPage ? 'disabled' : ''}`}>
                        <button className="page-link" onClick={() => setCurrentPage((p) => p + 1)}>
                            Next
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    );

    return (
        <div className="container mt-4">
            <h3>All Orders</h3>
            <table className="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Updated At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    {orders.map((order) => {
                        const statusInfo = getStatusConfig(order.status);
                        return (
                            <tr key={order.id}>
                                <td>{order.id}</td>
                                <td>{order.user?.name}</td>
                                <td>{order.title}</td>
                                <td>${(order.amount / 100).toFixed(2)}</td>
                                <td>{new Date(order.updated_at).toLocaleString()}</td>
                                <td>
                                    <div className="dropdown">
                                        <button
                                            className={`btn btn-sm dropdown-toggle text-light bg-${statusInfo.color}`}
                                            type="button"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                            disabled={statusUpdating === order.id}
                                        >
                                            {statusUpdating === order.id ? 'Updating…' : statusInfo.label}
                                        </button>
                                        <div className="dropdown-menu">
                                            {orderStatuses
                                                .filter((s) => s.value !== order.status)
                                                .map((s) => (
                                                    <button
                                                        key={s.value}
                                                        className={`dropdown-item text-${s.color}`}
                                                        onClick={() => updateStatus(order.id, s.value)}
                                                    >
                                                        {s.label}
                                                    </button>
                                                ))}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        );
                    })}
                    {orders.length === 0 && (
                        <tr>
                            <td colSpan="6" className="text-center text-muted">
                                No orders found.
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>

            {renderPagination()}
        </div>
    );
}

export default OrderTable;