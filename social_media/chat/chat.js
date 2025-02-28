document.addEventListener("DOMContentLoaded", function () {
    const chatForm = document.getElementById("chat-form");
    const chatBox = document.getElementById("chat-box");
    const receiverId = document.getElementById("receiver_id").value;

    function fetchMessages() {
        fetch(`fetch.php?receiver_id=${receiverId}`)
            .then(response => response.text())
            .then(data => {
                chatBox.innerHTML = data;
            });
    }

    chatForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const message = document.getElementById("message").value;
        if (message.trim() !== "") {
            fetch("send.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `receiver_id=${receiverId}&message=${encodeURIComponent(message)}`
            }).then(() => {
                document.getElementById("message").value = "";
                fetchMessages();
            });
        }
    });

    setInterval(fetchMessages, 3000); // Fetch messages every 3 seconds
});
