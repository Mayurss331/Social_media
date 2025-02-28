function fetchNotifications() {
    fetch('notifications/fetch.php')
    .then(response => response.text())
    .then(data => {
        document.getElementById('notification-count').innerHTML = data;
    });
}

setInterval(fetchNotifications, 5000); // Refresh every 5 seconds
