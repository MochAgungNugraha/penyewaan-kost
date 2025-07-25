document.addEventListener("DOMContentLoaded", function () {
  const chatbotMessages = document.getElementById("chatbot-messages");
  const userInput = document.getElementById("user-input");
  const sendButton = document.getElementById("send-message");

  if (!chatbotMessages || !userInput || !sendButton) return;

  // Profile information for Hikaru
  const chatbotProfile = {
    name: "Hikaru",
    role: "Asisten Kost Sidoarjo",
    greeting:
      "Halo! Saya Hikaru, asisten virtual KostSidoarjo. Ada yang bisa saya bantu?",
    avatar: "H", // Initial for avatar
  };

  // Sample responses for the chatbot
  const responses = {
    harga:
      "Harga kost di Sidoarjo bervariasi tergantung lokasi dan fasilitas. Mulai dari Rp 800.000 hingga Rp 2.500.000 per bulan.",
    lokasi:
      "Kami memiliki kost di berbagai lokasi di Sidoarjo seperti Sidoarjo Pusat, Waru, Gedangan, dan Taman.",
    fasilitas:
      "Fasilitas umum termasuk WiFi, AC, kamar mandi dalam, dapur bersama, dan keamanan 24 jam.",
    "cara pesan":
      "Anda bisa memesan melalui website dengan memilih kost, mengisi formulir, dan melakukan pembayaran.",
    pembayaran: "Kami menerima pembayaran via transfer bank, GoPay, dan OVO.",
    kontak:
      "Anda bisa menghubungi kami di:\n- Telepon: (031) 1234567\n- Email: info@kostsidoarjo.com\n- WhatsApp: 081234567890",
    default:
      "Maaf, saya tidak mengerti pertanyaan Anda. Coba tanyakan tentang:\n- Harga kost\n- Lokasi kost\n- Fasilitas\n- Cara pemesanan\n- Pembayaran\n- Kontak kami",
  };

  // Greet user with Hikaru's profile
  addBotMessage(chatbotProfile.greeting);

  // Send message when button is clicked
  sendButton.addEventListener("click", sendMessage);

  // Send message when Enter is pressed
  userInput.addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
      sendMessage();
    }
  });

  function sendMessage() {
    const message = userInput.value.trim();
    if (message === "") return;

    addUserMessage(message);
    userInput.value = "";

    // Simulate bot thinking
    setTimeout(() => {
      const response = getBotResponse(message);
      addBotMessage(response);

      // Scroll to bottom of chat
      chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }, 500);
  }

  function addUserMessage(message) {
    const messageElement = document.createElement("div");
    messageElement.classList.add("user-message");
    messageElement.textContent = message;
    chatbotMessages.appendChild(messageElement);

    // Scroll to bottom of chat
    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
  }

  function addBotMessage(message) {
    const messageElement = document.createElement("div");
    messageElement.classList.add("bot-message");
    messageElement.textContent = message;
    chatbotMessages.appendChild(messageElement);
  }

  function getBotResponse(message) {
    message = message.toLowerCase();

    if (
      message.includes("harga") ||
      message.includes("biaya") ||
      message.includes("tarif") ||
      message.includes("mahal") ||
      message.includes("murah")
    ) {
      return responses["harga"];
    } else if (
      message.includes("lokasi") ||
      message.includes("daerah") ||
      message.includes("alamat") ||
      message.includes("dimana") ||
      message.includes("letak")
    ) {
      return responses["lokasi"];
    } else if (
      message.includes("fasilitas") ||
      message.includes("fitur") ||
      message.includes("amenitas") ||
      message.includes("ac") ||
      message.includes("wifi") ||
      message.includes("kamar mandi")
    ) {
      return responses["fasilitas"];
    } else if (
      message.includes("pesan") ||
      message.includes("booking") ||
      message.includes("sewa") ||
      message.includes("daftar") ||
      message.includes("registrasi")
    ) {
      return responses["cara pesan"];
    } else if (
      message.includes("bayar") ||
      message.includes("pembayaran") ||
      message.includes("transfer") ||
      message.includes("gopay") ||
      message.includes("ovo")
    ) {
      return responses["pembayaran"];
    } else if (
      message.includes("kontak") ||
      message.includes("hubung") ||
      message.includes("telepon") ||
      message.includes("whatsapp") ||
      message.includes("email")
    ) {
      return responses["kontak"];
    } else if (
      message.includes("hi") ||
      message.includes("halo") ||
      message.includes("hai") ||
      message.includes("p") ||
      message.includes("assalam")
    ) {
      return chatbotProfile.greeting;
    } else {
      return responses["default"];
    }
  }
});
