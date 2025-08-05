import React from 'react';

function FlashMessage({ message, show }) {
    if (!show) return null;

    return (
        <div
            className="alert alert-success position-fixed"
            style={{
                bottom: '20px',
                right: '20px',
                zIndex: 1050,
                transition: 'opacity 0.3s ease-in-out',
                opacity: show ? 1 : 0,
            }}
        >
            {message}
        </div>
    );
}

export default FlashMessage;
