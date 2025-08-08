import React, { useEffect, useState } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';
import FlashMessage from './ui/FlashMessage';
import DeleteConfirmModal from './ui/DeleteConfirmModal';
import UserFormModal from './ui/UserFormModal';
import AddMoneyModal from './ui/AddMoneyModal';

function UserTable() {
    const [users, setUsers] = useState([]);
    const [userToDelete, setUserToDelete] = useState(null);
    const [flashMessage, setFlashMessage] = useState('');
    const [showFlash, setShowFlash] = useState(false);
    const [editUser, setEditUser] = useState(null);
    const [showForm, setShowForm] = useState(false);
    const [userToCredit, setUserToCredit] = useState(null);

    const [currentPage, setCurrentPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [total, setTotal] = useState(0);

    const handleSuccess = (message) => {
        setFlashMessage(message);
        setTimeout(() => setFlashMessage(''), 3000);
    };

    const fetchUsers = async (page = 1) => {
        try {
            const res = await axios.get(`/api/admin/users?page=${page}`);
            setUsers(res.data.data);
            setCurrentPage(res.data.current_page);
            setLastPage(res.data.last_page);
        } catch (err) {
            console.error('Failed to fetch users:', err);
        }
    };

    useEffect(() => {
        fetchUsers(currentPage);
    }, [currentPage]);

    const deleteUser = async (id) => {
        try {
            const csrfToken = Cookies.get('XSRF-TOKEN');
            await axios.delete(`/api/admin/users/${id}`, {
                headers: {
                    'X-XSRF-TOKEN': decodeURIComponent(csrfToken),
                },
            });

            setFlashMessage('✅ User deleted successfully!');
            setShowFlash(true);
            fetchUsers();

            setTimeout(() => {
                setShowFlash(false);
                setFlashMessage('');
            }, 3000);
        } catch (error) {
            console.error('Delete failed:', error.response?.data || error.message);
        }
    };

    const openEditModal = (user) => {
        setEditUser(user);
        setShowForm(true);
    };


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
        <>
            <FlashMessage message={flashMessage} show={showFlash} />
            <div className="d-flex justify-content-between align-items-center mb-3">
                <h3 className="mb-0">All Users</h3>
                <button className="btn btn-success" onClick={() => { setEditUser(null); setShowForm(true); }}>
                    Create User
                </button>
            </div>
            <table className="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {users.map(user => (
                        <tr key={user.id}>
                            <td>
                                <a
                                    href={`/admin/users/${user.id}`}
                                    className="text-primary"
                                    style={{ textDecoration: 'underline' }}
                                >
                                    {user.name}
                                </a>
                            </td>
                            <td>{user.email}</td>
                            <td>${(user.amount / 100).toFixed(2)}</td>
                            <td>
                                <button
                                    className="btn btn-sm btn-outline-info mr-2"
                                    onClick={() => openEditModal(user)}
                                >
                                    Edit
                                </button>
                                <button
                                    className="btn btn-sm btn-outline-danger mr-2"
                                    onClick={() => setUserToDelete(user)}
                                >
                                    Delete
                                </button>
                                <button
                                    className="btn btn-sm btn-outline-success mr-2"
                                    onClick={() => setUserToCredit(user)}
                                >
                                    Add Money
                                </button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
            <UserFormModal
                show={showForm}
                user={editUser}
                onClose={() => setShowForm(false)}
                onSave={() => {
                    setShowForm(false);
                    fetchUsers();
                    setFlashMessage(editUser ? '✅ User updated!' : '✅ User created!');
                    setShowFlash(true);
                    setTimeout(() => {
                        setShowFlash(false);
                        setFlashMessage('');
                    }, 3000);
                }}
            />

            <DeleteConfirmModal
                user={userToDelete}
                onCancel={() => setUserToDelete(null)}
                onConfirm={(id) => {
                    deleteUser(id);
                    setUserToDelete(null);
                }}
            />
            {userToCredit && (
                <AddMoneyModal
                    show={!!userToCredit}
                    onClose={() => setUserToCredit(null)}
                    submitLabel="Add Money"
                    showDescription={true}
                    showType={true}
                    showTitle={false}
                    onSuccess={(msg) => {
                        setFlashMessage(msg);
                        setShowFlash(true);
                        setTimeout(() => { setShowFlash(false); setFlashMessage(''); }, 3000);
                    }}
                    onSubmit={async ({ amount, description, type }) => {
                        await axios.get('/sanctum/csrf-cookie');
                        const csrf = Cookies.get('XSRF-TOKEN');
                        axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(csrf);
                        await axios.post('/api/admin/transactions', {
                            user_id: userToCredit.id,
                            amount,
                            description,
                            type,
                        });
                        fetchUsers();
                        handleSuccess(`✅ ${type === 'credit' ? 'Credited' : 'Debited'} successfully`);
                    }}
                />

            )}
            {renderPagination()}
        </>

    );
}

export default UserTable;
