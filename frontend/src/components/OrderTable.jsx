import React, { useEffect, useState } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';
import 'bootstrap/dist/js/bootstrap.bundle.min.js'; // for dropdowns

function OrderTable() {
    const [orders, setOrders] = useState([]);
    const [statusUpdating, setStatusUpdating] = useState(null);
    const [orderStatuses, setOrderStatuses] = useState([]);

    useEffect(() => {
        fetchOrders();
        fetchOrderStatuses();
    }, []);

    const fetchOrders = async () => {
        try {
            const res = await axios.get('/api/admin/orders');
            setOrders(res.data);
        } catch (err) {
            console.error('Failed to fetch orders:', err);
        }
    };

    const fetchOrderStatuses = async () => {
        try {
            const res = await axios.get('/api/admin/orders/statuses');
            setOrderStatuses(res.data); // expected to be array of { value, label, color }
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
                {
                    headers: {
                        'X-XSRF-TOKEN': decodeURIComponent(csrfToken),
                    },
                }
            );
            await fetchOrders();
        } catch (err) {
            console.error('Failed to update order:', err.response?.data || err.message);
        } finally {
            setStatusUpdating(null);
        }
    };

    const getStatusConfig = (statusKey) => {
        return (
            orderStatuses.find((s) => s.value === statusKey) || {
                label: statusKey,
                color: 'secondary',
            }
        );
    };

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
                                        >
                                            {statusInfo.label}
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
                </tbody>
            </table>
        </div>
    );
}

export default OrderTable;
