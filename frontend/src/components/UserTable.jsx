import React, { useEffect, useState } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';
import FlashMessage from './ui/FlashMessage';
import DeleteConfirmModal from './ui/DeleteConfirmModal';
import UserFormModal from './ui/UserFormModal';

function UserTable() {
    const [users, setUsers] = useState([]);
    const [userToDelete, setUserToDelete] = useState(null);
    const [flashMessage, setFlashMessage] = useState('');
    const [showFlash, setShowFlash] = useState(false);
    const [editUser, setEditUser] = useState(null);
    const [showForm, setShowForm] = useState(false);

    const fetchUsers = async () => {
        try {
            const res = await axios.get('/api/admin/users');
            setUsers(res.data);
        } catch (err) {
            console.error('Failed to fetch users:', err);
        }
    };

    useEffect(() => {
        fetchUsers();
    }, []);

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

    return (
        <>
            <FlashMessage message={flashMessage} show={showFlash} />
            <div className="d-flex justify-content-end mb-3">
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
                                    className="btn btn-sm btn-outline-danger"
                                    onClick={() => setUserToDelete(user)}
                                >
                                    Delete
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
        </>

    );
}

export default UserTable;
