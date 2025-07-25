<?php
session_start();
require_once 'php/db.php';
require_once 'php/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Get kost details if kost_id is provided
if (isset($_GET['id'])) {
    $kost_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM kost WHERE id = ?");
    $stmt->execute([$kost_id]);
    $kost = $stmt->fetch();
    
    if (!$kost) {
        header("Location: locations.html");
        exit();
    }
}

// Get booking details if booking_id is provided (for payment)
if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
    $stmt = $pdo->prepare("SELECT b.*, k.name as kost_name, k.location, k.price_per_month 
                          FROM bookings b 
                          JOIN kost k ON b.kost_id = k.id 
                          WHERE b.id = ? AND b.user_id = ?");
    $stmt->execute([$booking_id, $_SESSION['user_id']]);
    $booking = $stmt->fetch();
    
    if (!$booking) {
        header("Location: dashboard.php");
        exit();
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['book_kost'])) {
        // New booking
        $kost_id = $_POST['kost_id'];
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];
        $duration = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24 * 30); // months
        $total_price = $kost['price_per_month'] * $duration;
        
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, kost_id, check_in_date, check_out_date, total_price, status) 
                              VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$_SESSION['user_id'], $kost_id, $check_in, $check_out, $total_price]);
        
        header("Location: dashboard.php");
        exit();
    } elseif (isset($_POST['pay_booking'])) {
        // Payment for existing booking
        $booking_id = $_POST['booking_id'];
        
        // In a real app, you would integrate with a payment gateway here
        // For demo, we'll just update the status
        
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'Paid', payment_date = NOW() WHERE id = ? AND user_id = ?");
        $stmt->execute([$booking_id, $_SESSION['user_id']]);
        
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Kost Sidoarjo</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>KostSidoarjo</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="locations.html">Lokasi Kost</a></li>
                    <li><a href="news.php">Berita</a></li>
                    <li><a href="dashboard.php">Dashboard</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="checkout">
        <div class="container">
            <?php if (isset($kost)): ?>
                <h2>Pesan Kost <?php echo htmlspecialchars($kost['name']); ?></h2>
                
                <div class="checkout-grid">
                    <div class="kost-summary">
                        <img src="images/<?php echo htmlspecialchars($kost['image']); ?>" alt="<?php echo htmlspecialchars($kost['name']); ?>">
                        <h3><?php echo htmlspecialchars($kost['name']); ?></h3>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($kost['location']); ?></p>
                        <p><i class="fas fa-money-bill-wave"></i> Rp <?php echo number_format($kost['price_per_month'], 0, ',', '.'); ?>/bulan</p>
                        <p><?php echo htmlspecialchars($kost['description']); ?></p>
                    </div>
                    
                    <div class="booking-form">
                        <form method="POST">
                            <input type="hidden" name="kost_id" value="<?php echo $kost['id']; ?>">
                            
                            <div class="form-group">
                                <label for="check_in">Tanggal Masuk</label>
                                <input type="date" id="check_in" name="check_in" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="check_out">Tanggal Keluar</label>
                                <input type="date" id="check_out" name="check_out" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="duration">Durasi (bulan)</label>
                                <input type="text" id="duration" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="total_price">Total Harga</label>
                                <input type="text" id="total_price" readonly>
                            </div>
                            
                            <button type="submit" name="book_kost" class="btn">Pesan Sekarang</button>
                        </form>
                    </div>
                </div>
                
            <?php elseif (isset($booking)): ?>
                <h2>Pembayaran untuk <?php echo htmlspecialchars($booking['kost_name']); ?></h2>
                
                <div class="checkout-grid">
                    <div class="booking-summary">
                        <h3>Detail Pemesanan</h3>
                        <p><strong>Kost:</strong> <?php echo htmlspecialchars($booking['kost_name']); ?></p>
                        <p><strong>Lokasi:</strong> <?php echo htmlspecialchars($booking['location']); ?></p>
                        <p><strong>Check-in:</strong> <?php echo date('d M Y', strtotime($booking['check_in_date'])); ?></p>
                        <p><strong>Check-out:</strong> <?php echo date('d M Y', strtotime($booking['check_out_date'])); ?></p>
                        <p><strong>Durasi:</strong> <?php echo round((strtotime($booking['check_out_date']) - strtotime($booking['check_in_date'])) / (60 * 60 * 24 * 30)); ?> bulan</p>
                        <p><strong>Total Harga:</strong> Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?></p>
                        <p><strong>Status:</strong> <?php echo $booking['status']; ?></p>
                    </div>
                    
                    <div class="payment-form">
                        <form method="POST">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            
                            <h3>Metode Pembayaran</h3>
                            <div class="payment-methods">
                                <div class="payment-method">
                                    <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" checked>
                                    <label for="bank_transfer">Transfer Bank</label>
                                </div>
                                <div class="payment-method">
                                    <input type="radio" id="gopay" name="payment_method" value="gopay">
                                    <label for="gopay">GoPay</label>
                                </div>
                                <div class="payment-method">
                                    <input type="radio" id="ovo" name="payment_method" value="ovo">
                                    <label for="ovo">OVO</label>
                                </div>
                            </div>
                            
                            <button type="submit" name="pay_booking" class="btn">Bayar Sekarang</button>
                        </form>
                    </div>
                </div>
                
            <?php else: ?>
                <h2>Checkout</h2>
                <p>Silakan pilih kost terlebih dahulu.</p>
                <a href="locations.html" class="btn">Cari Kost</a>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <!-- Same footer as index.html -->
    </footer>

    <!-- WhatsApp and Chatbot elements same as index.html -->

    <script src="js/main.js"></script>
    <?php if (isset($kost)): ?>
    <script>
        // Calculate duration and total price
        document.getElementById('check_in').addEventListener('change', calculatePrice);
        document.getElementById('check_out').addEventListener('change', calculatePrice);
        
        function calculatePrice() {
            const checkIn = new Date(document.getElementById('check_in').value);
            const checkOut = new Date(document.getElementById('check_out').value);
            
            if (checkIn && checkOut && checkOut > checkIn) {
                const duration = (checkOut - checkIn) / (1000 * 60 * 60 * 24 * 30); // months
                const pricePerMonth = <?php echo $kost['price_per_month']; ?>;
                const totalPrice = duration * pricePerMonth;
                
                document.getElementById('duration').value = duration.toFixed(1) + ' bulan';
                document.getElementById('total_price').value = 'Rp ' + totalPrice.toLocaleString('id-ID');
            }
        }
    </script>
    <?php endif; ?>
</body>
</html>