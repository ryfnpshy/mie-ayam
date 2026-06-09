# 🍜 Project Requirements Document (PRD): Bakmi Ayam Kembar

## 📋 1. Project Metadata
* **Project Name:** Web Pemesanan Online Bakmi Ayam Kembar
* **Lead Developer:** Rayfan Pashya
* **Tech Stack:** Laravel 13, PHP 8.3
* **Target Audience:** Customer (Guest Checkout) & Administrator
* **Design Approach:** Mobile-First Design
* **Color Palette:** Primary (Yellow & Red), Accent/Text (Minimalist Black)

---

## 🤖 2. Instructions for AI Agent
**[SYSTEM CONTEXT]**
You are an expert full-stack Laravel 13 and PHP 8.3 developer. Your task is to build a high-performance, mobile-first web application based on this document. 
* Strictly adhere to PHP 8.3 syntax (e.g., typed class constants, read-only classes where applicable).
* Utilize Laravel 13 native features, including Native PHP Attributes for routing/models.
* Use **Laravel Reverb** for all real-time broadcasting and notifications.
* Do not implement user authentication for the Customer facing front-end (Guest Checkout only).

---

## 👥 3. User Roles & Features

### 3.1. Customer (Guest)
Customers do not need to register or login.
* **Homepage:** View all menus, view "Best Seller" section, check store open/close status.
* **Menu Detail:** View Title, Description, Image, Average Rating, and previous Reviews.
* **Cart & Add-ons:**
  * Global Add-ons (applicable to all menus): Pangsit, Bakso, Ceker, Sayap.
  * Specific Variants (conditionally rendered): "Level Kepedasan" (Spiciness Level) ONLY for *Mie Chili Oil*, *Mie Yamin*, and *Mie Pasir Oil*.
* **Checkout & GPS:**
  * Input Name, WhatsApp Number, and Address.
  * System requests GPS location to calculate distance to the store.
  * Shipping cost calculation: `Distance (KM) * Rp5.000`.
* **Payment:** Display static QRIS barcode. User clicks "Saya Sudah Bayar" (I Have Paid) to submit.
* **Post-Order:** 
  * Track order status via a unique generated URL.
  * Click "Pesanan Diterima" (Order Received) when food arrives.
  * Submit a Star Rating (1-5) and Text Review (automatically syncs to the menu detail).

### 3.2. Administrator
Dashboard for store and order management.
* **Menu Management (CRUD):** Add, Edit, Delete menus. Manage stock status (Available/Out of Stock).
* **Add-on Management:** Set prices and availability for toppings.
* **Operational Management:** Set store Open/Close hours.
* **Order Management (Real-Time):**
  * Receive real-time pop-up/audio notifications for new orders via Laravel Reverb.
  * **Manual Payment Validation:** Admin checks bank mutations, then clicks "Konfirmasi Pembayaran" (Confirm Payment).
  * Update Status: `Menunggu Konfirmasi` -> `Diproses` -> `Dikirim`.
* **Finance & Analytics:**
  * Calculate and display Daily Profit.
  * View Best Selling menus, Slow Moving menus, and Highest Rated menus.
  * View customer notes/reviews.

---

## 🗄️ 4. Conceptual Database Schema (For AI Reference)

The AI Agent should generate Laravel Migrations and Models based on these core entities:

* **Admins:** `id`, `name`, `email`, `password`
* **Settings:** `id`, `key` (e.g., store_status, shipping_rate), `value`
* **Menus:** `id`, `name`, `description`, `image_path`, `price`, `is_available`, `is_spicy_variant_enabled`
* **AddOns:** `id`, `name`, `price`
* **Orders:** `id`, `order_number` (unique), `customer_name`, `customer_wa`, `customer_address`, `distance_km`, `shipping_cost`, `total_price`, `status` (pending, paid, processing, shipped, completed), `payment_proof_status`
* **Order_Items:** `id`, `order_id`, `menu_id`, `quantity`, `spiciness_level` (nullable), `notes`
* **Order_Item_AddOns:** `id`, `order_item_id`, `addon_id`
* **Reviews:** `id`, `menu_id`, `order_id`, `rating` (1-5), `comment`

---

## 🔄 5. State Machine: Order Flow
1. **Pending:** Order submitted, waiting for payment validation.
2. **Paid:** Admin confirmed the QRIS payment.
3. **Processing:** Kitchen is preparing the food.
4. **Shipped:** Admin marked as sent via courier.
5. **Completed:** Customer clicked "Order Received".