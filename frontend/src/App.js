import React, { useEffect, useState, useRef } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';
import Login from './components/Login';
import Navbar from './components/Navbar';

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
    <div>
    <Navbar user={user} onLogout={logout} />
    <div className="container mt-4">
      <h2>Welcome, {user.name}</h2>
      <p>Email: {user.email}</p>
    </div>
  </div>
  );
}

export default App;
