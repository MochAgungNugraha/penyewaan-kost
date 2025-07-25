// Mobile menu toggle for small screens
document.addEventListener("DOMContentLoaded", function () {
  const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
  const nav = document.querySelector("nav ul");

  if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener("click", function () {
      nav.classList.toggle("active");
    });
  }

  // Chatbot toggle
  const chatbotToggler = document.querySelector(".chatbot-toggler");
  const chatbotContainer = document.querySelector(".chatbot-container");
  const closeChatbot = document.querySelector(".close-chatbot");

  if (chatbotToggler && chatbotContainer) {
    chatbotToggler.addEventListener("click", function () {
      chatbotContainer.classList.toggle("active");
    });
  }

  if (closeChatbot) {
    closeChatbot.addEventListener("click", function () {
      chatbotContainer.classList.remove("active");
    });
  }

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();

      const targetId = this.getAttribute("href");
      if (targetId === "#") return;

      const targetElement = document.querySelector(targetId);
      if (targetElement) {
        targetElement.scrollIntoView({
          behavior: "smooth",
        });
      }
    });
  });

  // Form validation for checkout
  const bookingForm = document.querySelector(".booking-form form");
  if (bookingForm) {
    bookingForm.addEventListener("submit", function (e) {
      const checkIn = document.getElementById("check_in");
      const checkOut = document.getElementById("check_out");

      if (new Date(checkOut.value) <= new Date(checkIn.value)) {
        e.preventDefault();
        alert("Tanggal keluar harus setelah tanggal masuk");
      }
    });
  }

  // Initialize date pickers
  const today = new Date().toISOString().split("T")[0];
  const checkInInputs = document.querySelectorAll(
    'input[type="date"][id="check_in"]'
  );
  checkInInputs.forEach((input) => {
    input.min = today;
  });

  // Filter functionality for locations page
  const filterBtn = document.querySelector(".filter-btn");
  if (filterBtn) {
    filterBtn.addEventListener("click", function () {
      // Filter implementation would go here
      console.log("Filtering locations...");
    });
  }
});
