<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Mengambil semua notifikasi (misal, 10 terbaru)
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->take(10)->get();
        return response()->json($notifications);
    }

    // Menghitung notifikasi yang belum dibaca
    public function unreadCount()
    {
        $count = Auth::user()->notifications()->whereNull('read_at')->count();
        return response()->json(['count' => $count]);
    }

    // Menandai semua notifikasi sebagai telah dibaca
    public function markAllAsRead()
    {
        Auth::user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['message' => 'Semua notifikasi ditandai sebagai telah dibaca.']);
    }
}