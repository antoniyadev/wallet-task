import React from 'react';
import UserTable from './UserTable';

function AdminDashboard() {
    return (
        <div className="container mt-4">
            <h3>All Users</h3>
            <UserTable />
        </div>
    );
}

export default AdminDashboard;
