// Member Payments - View history and make payments
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#paymentsTable tbody');
    const alertContainer = document.getElementById('alertContainer');
    const paymentModal = document.getElementById('paymentModal');
    const paymentForm = document.getElementById('paymentForm');
    const planSelect = document.getElementById('plan_id');
    const amountInput = document.getElementById('plan_amount');

    loadPayments();
    loadPlans();

    if (paymentForm) {
        paymentForm.addEventListener('submit', function (e) {
            e.preventDefault();
            makePayment();
        });
    }

    // Update amount when plan is selected
    if (planSelect) {
        planSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                amountInput.value = '৳' + parseFloat(price).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                amountInput.value = '';
            }
        });
    }

    function loadPayments() {
        fetch('/api/payments', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    displayPayments(data.data);
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No payments found</td></tr>';
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">Error loading payments</td></tr>';
            });
    }

    function displayPayments(payments) {
        if (payments.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-6);color:var(--text-muted);">No payment history yet. Make your first payment!</td></tr>';
            return;
        }

        tbody.innerHTML = payments.map(payment => `
      <tr>
        <td>${formatDate(payment.payment_date)}</td>
        <td>${escapeHtml(payment.plan_name)}</td>
        <td><strong>৳${parseFloat(payment.amount).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
        <td><span class="badge badge-neutral">${formatPaymentMethod(payment.payment_method)}</span></td>
        <td><span class="badge ${getStatusBadge(payment.status)}">${formatStatus(payment.status)}</span></td>
      </tr>
    `).join('');
    }

    function loadPlans() {
        fetch('/api/plans', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    populatePlans(data.data);
                }
            })
            .catch(err => console.error('Error loading plans:', err));
    }

    function populatePlans(plans) {
        const activePlans = plans.filter(p => p.is_active == 1);
        planSelect.innerHTML = '<option value="">Choose a plan</option>' +
            activePlans.map(plan =>
                `<option value="${plan.id}" data-price="${plan.price}">${escapeHtml(plan.name)} - ৳${parseFloat(plan.price).toLocaleString('en-BD')} (${plan.duration_months} month${plan.duration_months > 1 ? 's' : ''})</option>`
            ).join('');
    }

    window.openPaymentModal = function () {
        paymentModal.style.display = 'flex';
        document.getElementById('paymentModalError').classList.add('hide');
    };

    window.closePaymentModal = function () {
        paymentModal.style.display = 'none';
        paymentForm.reset();
        amountInput.value = '';
        document.getElementById('paymentModalError').classList.add('hide');
    };

    function makePayment() {
        const formData = new FormData(paymentForm);
        const data = {
            plan_id: parseInt(formData.get('plan_id')),
            payment_method: formData.get('payment_method'),
            transaction_id: formData.get('transaction_id') || null,
            notes: formData.get('notes') || null
        };

        if (!data.plan_id) {
            showModalError('paymentModalError', 'Please select a membership plan');
            return;
        }

        if (!data.payment_method) {
            showModalError('paymentModalError', 'Please select a payment method');
            return;
        }

        fetch('/api/payments/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    closePaymentModal();
                    showAlert(response.message || 'Payment successful!', 'success');
                    loadPayments(); // Reload payment history
                    // Optionally reload membership status
                    setTimeout(() => {
                        window.location.href = '/member/membership';
                    }, 2000);
                } else {
                    showModalError('paymentModalError', response.message || 'Payment failed');
                }
            })
            .catch(err => {
                showModalError('paymentModalError', 'Error processing payment. Please try again.');
            });
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function formatPaymentMethod(method) {
        const methods = {
            'cash': 'Cash',
            'card': 'Card',
            'online': 'Online',
            'bank_transfer': 'Bank Transfer'
        };
        return methods[method] || method;
    }

    function formatStatus(status) {
        const statuses = {
            'completed': 'Completed',
            'pending': 'Pending',
            'failed': 'Failed',
            'refunded': 'Refunded'
        };
        return statuses[status] || status;
    }

    function getStatusBadge(status) {
        const badges = {
            'completed': 'badge-success',
            'pending': 'badge-warning',
            'failed': 'badge-error',
            'refunded': 'badge-neutral'
        };
        return badges[status] || 'badge-neutral';
    }

    function showAlert(message, type) {
        alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }

    function showModalError(elementId, message) {
        const el = document.getElementById(elementId);
        el.textContent = message;
        el.classList.remove('hide');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    window.onclick = function (event) {
        if (event.target === paymentModal) {
            closePaymentModal();
        }
    };
});
