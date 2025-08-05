import React from 'react';

function Navbar({ user, onLogout }) {
  return (
    <nav className="navbar navbar-expand-lg navbar-light bg-light py-1 px-3">
      <div className="container">
      <a className="navbar-brand d-flex align-items-center" href="#">
  <img
     src="/wallet.png"
    alt="Wallet"
    width="60"
    height="60"
    className="me-3"
    style={{ objectFit: 'contain' }}
  />
  <span className="fs-1 fw-bold">Wallet</span>
</a>
        <div className="ml-auto d-flex align-items-center">
            <span className="navbar-text mr-3">{user.email}</span>
            <button className="btn btn-outline-danger btn-sm" onClick={onLogout}>
                Logout
            </button>
        </div>
      </div>
    </nav>
  );
}

export default Navbar;
