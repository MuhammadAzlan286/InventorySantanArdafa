/**
 * Global Notification Logic
 * Handles toggling, marking as read, and closing on outside click.
 */

// Toggle Notification Dropdown
window.toggleNotification = function(event) {
    event.stopPropagation();
    const container = document.getElementById('notificationContainer');
    if (container) {
        container.classList.toggle('active');
    }
};

// Close when clicking outside
document.addEventListener('click', function(e) {
    const container = document.getElementById('notificationContainer');
    if (container && !container.contains(e.target)) {
        container.classList.remove('active');
    }
});

// Mark Single Notification as Read
window.markNotificationAsRead = function(element, id) {
    // Visual feedback
    element.style.pointerEvents = 'none';
    element.style.background = 'rgba(0,0,0,0.03)';

    // Adjust path if needed, assuming relative execution from dashboard folder
    fetch(`mark_read.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                element.style.transition = 'all 0.4s ease';
                element.style.opacity = '0';
                element.style.transform = 'translateX(20px)';
                
                setTimeout(() => {
                    element.remove();
                    // Update badge
                    const badge = document.getElementById('bellBadge');
                    if (badge) {
                        let count = parseInt(badge.innerText);
                        count--;
                        if (count <= 0) {
                            badge.remove();
                        } else {
                            badge.innerText = count;
                        }
                    }
                    checkNotificationsEmpty();
                }, 400);
            } else {
                element.style.pointerEvents = 'auto';
                element.style.background = '';
                alert("Error: " + (data.message || "Gagal memproses."));
            }
        })
        .catch(error => {
            element.style.pointerEvents = 'auto';
            element.style.background = '';
            console.error('Error:', error);
            // Silent fail or alert
        });
};

// Mark All as Read
window.markAllNotificationsAsRead = function() {
    if (!confirm("Tandai semua sebagai sudah dibaca?")) return;

    fetch(`mark_read.php?all=1`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Animate removal
                const list = document.getElementById('notificationList');
                if (list) {
                    const items = list.querySelectorAll('.dropdown-item');
                    items.forEach(item => {
                        item.style.transition = 'all 0.4s ease';
                        item.style.opacity = '0';
                    });
                }
                
                setTimeout(() => {
                    location.reload(); 
                }, 400);
            } else {
                alert("Gagal: " + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Terjadi kesalahan koneksi.");
        });
};

// Helper: Check Empty State
function checkNotificationsEmpty() {
    const list = document.getElementById('notificationList');
    if (!list) return;
    
    // Check if any visible items remain (ignoring those being removed)
    // For simplicity, we just check querySelector length after removal
    const items = list.querySelectorAll('.dropdown-item');
    if (items.length === 0) {
        list.innerHTML = `
            <div class="dropdown-item" style="text-align:center; color:#9c9fa6; padding: 30px;">
                <i class="fas fa-bell-slash" style="font-size: 1.5rem; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
                Tidak ada notifikasi baru
            </div>
        `;
        const badge = document.getElementById('bellBadge');
        if (badge) badge.remove();
    }
}
