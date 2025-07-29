// File: public/firebase-messaging-sw.js

// Import dan inisialisasi Firebase (harus ada, bahkan jika kosong)
// Skrip di bawah ini disediakan oleh Firebase dan penting untuk fungsionalitasnya
importScripts("https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js");

// Inisialisasi aplikasi Firebase di Service Worker
// GANTI DENGAN KONFIGURASI FIREBASE ANDA
const firebaseConfig = {
  apiKey: "AIzaSyBxGFv8ZZUpBkS7InHxy6dc9ozM8uVk-Oc",
  authDomain: "website-finance-mahasiswa.firebaseapp.com",
  projectId: "website-finance-mahasiswa",
  storageBucket: "website-finance-mahasiswa.firebasestorage.app",
  messagingSenderId: "802146642423",
  appId: "1:802146642423:web:2a081831a4f8460700a6f2",
  measurementId: "G-RM7G449JSD"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Menangani notifikasi yang masuk saat aplikasi berada di background
messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: 'images/logo.png', // Ganti dengan path logo Anda
        data: {
            url: payload.data.url // Menyimpan URL dari data notifikasi
        }
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Menangani event klik pada notifikasi
self.addEventListener('notificationclick', function(event) {
    event.notification.close(); // Menutup notifikasi
    
    const urlToOpen = event.notification.data.url || '/'; // Fallback ke halaman utama

    event.waitUntil(
        clients.openWindow(urlToOpen)
    );
});