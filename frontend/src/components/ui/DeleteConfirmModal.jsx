import React from 'react';

function DeleteConfirmModal({ user, onCancel, onConfirm }) {
    if (!user) return null;

    return (
        <div
            className="modal fade show d-block"
            tabIndex="-1"
            role="dialog"
            style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}
        >
            <div className="modal-dialog" role="document">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title">Confirm Deletion</h5>
                        <button type="button" className="close" onClick={onCancel}>
                            <span>&times;</span>
                        </button>
                    </div>
                    <div className="modal-body">
                        <p>
                            Are you sure you want to delete <strong>{user.name}</strong>?
                        </p>
                    </div>
                    <div className="modal-footer">
                        <button className="btn btn-secondary" onClick={onCancel}>
                            Cancel
                        </button>
                        <button className="btn btn-danger" onClick={() => onConfirm(user.id)}>
                            Yes, Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default DeleteConfirmModal;
