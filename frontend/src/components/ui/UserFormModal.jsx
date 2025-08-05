import React, { useState, useEffect } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';
import InputField from './InputField';
import SelectField from './SelectField';

function UserFormModal({ show, onClose, onSave, user }) {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        role_id: '',
        password: '',
    });

    const [roles, setRoles] = useState([]);
    const [errors, setErrors] = useState({});

    useEffect(() => {
        if (user) {
            setFormData({
                name: user.name || '',
                email: user.email || '',
                role_id: user.role_id || '',
                password: '', // don't preload passwords
            });
        } else {
            setFormData({ name: '', email: '', role_id: '', password: '' });
        }
    }, [user]);

    useEffect(() => {
        if (show) {
            axios.get('/api/admin/roles')
                .then(res => setRoles(res.data))
                .catch(err => console.error('Failed to fetch roles:', err));
        }
    }, [show]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const csrfToken = Cookies.get('XSRF-TOKEN');

        try {
            const payload = { ...formData };

            const config = {
                headers: {
                    'X-XSRF-TOKEN': decodeURIComponent(csrfToken),
                },
            };

            if (user) {
                await axios.put(`/api/admin/users/${user.id}`, payload, config);
            } else {
                await axios.post('/api/admin/users', payload, config);
            }

            setErrors({}); // clear errors
            onSave();
        } catch (err) {
            if (err.response?.status === 422) {
                setErrors(err.response.data.errors);
            } else {
                console.error('Failed to save user:', err.response?.data || err.message);
            }
        }
    };

    if (!show) return null;

    return (
        <div className="modal fade show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
            <div className="modal-dialog">
                <div className="modal-content">
                    <form onSubmit={handleSubmit}>
                        <div className="modal-header">
                            <h5 className="modal-title">{user ? 'Edit User' : 'Create User'}</h5>
                            <button type="button" className="close" onClick={onClose}><span>&times;</span></button>
                        </div>
                        <div className="modal-body">
                            <InputField
                                label="Name"
                                name="name"
                                value={formData.name}
                                onChange={handleChange}
                                error={errors.name}
                            />

                            <InputField
                                label="Email"
                                name="email"
                                type="email"
                                value={formData.email}
                                onChange={handleChange}
                                error={errors.email}
                            />

                            <InputField
                                label="Password"
                                name="password"
                                type="password"
                                value={formData.password}
                                onChange={handleChange}
                                error={errors.password}
                            />

                            <SelectField
                                label="Role"
                                name="role_id"
                                value={formData.role_id}
                                onChange={handleChange}
                                options={roles}
                                error={errors.role_id}
                            />
                        </div>
                        <div className="modal-footer">
                            <button type="submit" className="btn btn-primary">{user ? 'Update' : 'Create'}</button>
                            <button type="button" className="btn btn-secondary" onClick={onClose}>Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}

export default UserFormModal;
