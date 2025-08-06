import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import React, { useEffect, useState, useRef } from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import axios from 'axios';
import Cookies from 'js-cookie';
import Login from './components/Login';
import Navbar from './components/Navbar';
import AdminDashboard from './components/AdminDashboard';
import MerchantDashboard from './components/MerchantDashboard';
import UserDetail from './components/UserDetail';
import OrderTable from './components/OrderTable';

axios.defaults.baseURL = 'http://localhost:8080';
axios.defaults.withCredentials = true;

function App() {
    const [user, setUser] = useState(null);
    const [status, setStatus] = useState('loading');

    const hasRun = useRef(false); // prevents double execution
    const fetchUser = async () => {
        try {
            const response = await axios.get('/api/user');
            setUser(response.data);
            setStatus('authenticated');
        } catch (err) {
            setUser(null);
            setStatus('unauthenticated');
        }
    };

    const logout = async () => {
        try {
            // Get CSRF cookie
            await axios.get('/sanctum/csrf-cookie');

            // Read the token from cookies and set header
            const csrfToken = Cookies.get('XSRF-TOKEN');
            if (csrfToken) {
                axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(csrfToken);
            } else {
                console.error('CSRF token missing');
                return;
            }

            await axios.post('/logout');

            setUser(null);
            setStatus('unauthenticated');
        } catch (error) {
            console.error('Logout failed:', error.response?.data || error.message);
        }
    };

    useEffect(() => {
        if (!hasRun.current) {
            hasRun.current = true;
            fetchUser();
        }
    }, []);

    if (status === 'loading') return <p>Loading...</p>;
    if (!user) return <Login onLogin={fetchUser} />;

    return (
        <Router>
            <Navbar user={user} onLogout={logout} />

            <div className="container mt-4">
                <Routes>
                    {user.role === 'admin' && (
                        <>
                            <Route path="/admin" element={<Navigate to="/admin/users" replace />} />
                            <Route path="/admin/users" element={<AdminDashboard />} />
                            <Route path="/admin/users/:id" element={<UserDetail />} />
                            <Route path="/admin/orders" element={<OrderTable />} />
                        </>
                    )}
                    {user.role === 'merchant' && (
                        <Route path="/merchant" element={<MerchantDashboard />} />
                    )}
                </Routes>
            </div>
        </Router>
    );
}

export default App
