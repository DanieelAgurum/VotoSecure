const chatToggle = document.getElementById("chatToggle");
const chatPanel = document.getElementById("chatPanel");
const chatClose = document.getElementById("chatClose");

chatToggle.addEventListener("click", () => {
    chatPanel.classList.toggle("active");
});

chatClose.addEventListener("click", () => {
    chatPanel.classList.remove("active");
});
