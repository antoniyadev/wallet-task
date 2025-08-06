import React from 'react';
import { NavLink } from 'react-router-dom';

function Navbar({ user, onLogout }) {
    return (
        <nav className="navbar navbar-expand-lg navbar-light bg-light shadow-sm py-2">
            <div className="container d-flex justify-content-between align-items-center">

                {/* Logo */}
                <a className="navbar-brand d-flex align-items-center" href="/admin">
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

                {/* Center nav links (only for admin) */}
                {user.role === 'admin' && (
                    <div className="d-flex align-items-center">
                        <NavLink
                            to="/admin/users"
                            end
                            className={({ isActive }) =>
                                `nav-link px-3 ${isActive ? 'font-weight-bold text-primary' : 'text-dark'}`
                            }
                        >
                            Users
                        </NavLink>
                        <NavLink
                            to="/admin/orders"
                            className={({ isActive }) =>
                                `nav-link px-3 ${isActive ? 'font-weight-bold text-primary' : 'text-dark'}`
                            }
                        >
                            Orders
                        </NavLink>
                    </div>
                )}

                {/* Right side: Email + Logout */}
                <div className="d-flex align-items-center">
                    <span className="text-muted small mr-3">{user.email}</span>
                    <button className="btn btn-outline-danger btn-sm" onClick={onLogout}>
                        Logout
                    </button>
                </div>

            </div>
        </nav>


    );
}

export default Navbar;
