import React, { useState } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';

function Login({ onLogin }) {
    const [email, setEmail] = useState('merchant@example.com');
    const [password, setPassword] = useState('merchantpassword');
    const [error, setError] = useState(null);

    const handleLogin = async (e) => {
        e.preventDefault();

        try {
            await axios.get('/sanctum/csrf-cookie');

            const csrfToken = Cookies.get('XSRF-TOKEN');
            if (csrfToken) {
                axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(csrfToken);
            }

            await axios.post('/login', { email, password });
            onLogin();
        } catch (err) {
            setError('Invalid credentials.');
        }
    };

    return (
        <div className="d-flex justify-content-center align-items-center min-vh-100 bg-light">
            <div className="card p-4 shadow" style={{ minWidth: '320px' }}>
                <a className="navbar-brand d-flex align-items-center justify-content-center" href="/">
                    <img
                        src="/wallet.png"
                        alt="Wallet"
                        width="40"
                        height="40"
                        className="mr-2"
                        style={{ objectFit: 'contain' }}
                    />
                    <span className="h5 mb-0 font-weight-bold text-primary">Wallet</span>
                </a>
                {error && <div className="alert alert-danger">{error}</div>}
                <form onSubmit={handleLogin}>
                    <div className="mb-3">
                        <label>Email</label>
                        <input
                            type="email"
                            className="form-control"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            required
                        />
                    </div>
                    <div className="mb-3">
                        <label>Password</label>
                        <input
                            type="password"
                            className="form-control"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            required
                        />
                    </div>
                    <button className="btn btn-primary w-100" type="submit">
                        Login
                    </button>
                </form>
            </div>
        </div>
    );
}

export default Login;
