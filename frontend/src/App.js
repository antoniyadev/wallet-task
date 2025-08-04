import React, { useEffect, useState, useRef } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';

axios.defaults.baseURL = 'http://localhost:8080';
axios.defaults.withCredentials = true;

function App() {
  const [user, setUser] = useState(null);
  const [status, setStatus] = useState('loading');
  const hasRun = useRef(false); // 👈 prevents double execution

  const loginAndFetchUser = async () => {
    try {
      setStatus('authenticating');

      // Step 1: Get CSRF cookie
      await axios.get('/sanctum/csrf-cookie');

      // Step 2: Set CSRF token manually
      const csrfToken = Cookies.get('XSRF-TOKEN');
      if (csrfToken) {
        axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(csrfToken);
      } else {
        console.error('CSRF token missing');
        return;
      }

      // Step 3: Login
      await axios.post('/login', {
        email: 'merchant@example.com',
        password: 'merchantpassword',
      });

      // Step 4: Fetch authenticated user
      const response = await axios.get('/api/user');
      setUser(response.data);
      setStatus('authenticated');
    } catch (error) {
      console.error('Auth error:', error);
      setStatus('unauthenticated');
    }
  };

  useEffect(() => {
    if (!hasRun.current) {
      hasRun.current = true;
      loginAndFetchUser();
    }
  }, []);

  return (
    <div className="container mt-4">
      <h1>Wallet App</h1>
      {status === 'loading' && <p>Loading...</p>}
      {status === 'unauthenticated' && (
        <div className="alert alert-danger">❌ Could not log in.</div>
      )}
      {status === 'authenticated' && user && (
        <div className="card p-4">
          <h4>Welcome, {user.name}!</h4>
          <p>Email: {user.email}</p>
        </div>
      )}
    </div>
  );
}

export default App;
