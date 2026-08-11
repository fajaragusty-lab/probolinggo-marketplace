# BersolekMart

Marketplace UMKM Kota Probolinggo — CodeIgniter 4

## Requirements
- PHP 8.2+ (disarankan 8.3)
- MySQL 8+
- Composer
- Ekstensi: intl, mysqli, mbstring, curl, json

## Instalasi

```bash
composer install
cp .env.example .env
# edit .env: database, app.baseURL
php spark migrate
php spark db:seed DatabaseSeeder
php spark serve
```

Buka http://localhost:8080

## Demo accounts (password: `password`)
| Email | Role |
|-------|------|
| superadmin@marketplace.test | Super Admin |
| govadmin@marketplace.test | Government |
| customer@marketplace.test | Customer |
| umkm1@marketplace.test | UMKM |
| courier1@marketplace.test | Courier |

## aaPanel
- Document root: `/www/wwwroot/bersolekmart/public`
- Pastikan `writable/` writable (777 atau user www)
- Jangan expose folder `app/`, `vendor/`, `.env`

## Fitur yang sudah wire (Phase 1–3)
- Register / Login / Logout (session + password_hash)
- RBAC roles
- Katalog, search, kategori, toko, detail produk
- Cart (add/update/remove) → **persist ke MySQL**
- Alamat customer
- Checkout dengan transaksi + `SELECT ... FOR UPDATE` stock lock
- Order + order_items + payment + shipment per store
- UI mobile-first Bootstrap 5 + bottom nav
- PWA manifest + service worker (baseline)

## Struktur order
Parent `orders` + `order_items` (multi-store) + `shipments` per store.

## Catatan
Modul UMKM dashboard, Courier workflow lengkap, Government dashboard, feedback, dan payment Bank Jatim gateway interface dapat dilanjutkan di fase berikutnya di atas fondasi ini.
