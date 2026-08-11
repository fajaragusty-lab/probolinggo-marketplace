<?php

if (!function_exists('bm_currency')) {
    function bm_currency(int|float $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}

if (!function_exists('bm_image_url')) {
    function bm_image_url(?string $path, string $fallbackText = 'BersolekMart'): string
    {
        $clean = trim((string) $path);
        if ($clean === '') {
            return bm_placeholder_svg($fallbackText);
        }

        if (preg_match('#^https?://#i', $clean)) {
            return $clean;
        }

        $clean = ltrim($clean, '/');
        $absolute = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $clean;

        if (is_file($absolute)) {
            return base_url($clean);
        }

        return bm_placeholder_svg($fallbackText);
    }
}

if (!function_exists('bm_placeholder_svg')) {
    function bm_placeholder_svg(string $label): string
    {
        $safe = mb_substr(trim($label) ?: 'BersolekMart', 0, 28);
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><defs><linearGradient id="g" x1="0" x2="1" y1="0" y2="1"><stop stop-color="#f3f4f6" offset="0"/><stop stop-color="#e5e7eb" offset="1"/></linearGradient></defs><rect width="600" height="400" fill="url(#g)"/><circle cx="95" cy="95" r="42" fill="#d1d5db"/><path d="M0 335L150 215L285 305L395 185L600 335V400H0Z" fill="#cbd5e1"/><text x="50%" y="54%" text-anchor="middle" fill="#6b7280" font-family="Inter,Arial,sans-serif" font-size="28" font-weight="600">' . esc($safe) . '</text></svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}

if (!function_exists('bm_status_class')) {
    function bm_status_class(string $status): string
    {
        $status = strtoupper(trim($status));

        return match ($status) {
            'ACTIVE', 'COMPLETED', 'DELIVERED', 'PAID', 'VERIFIED', 'ONLINE', 'AVAILABLE' => 'is-success',
            'PENDING', 'PENDING_PAYMENT', 'COD_CONFIRMED', 'PROCESSING', 'READY_FOR_PICKUP', 'COURIER_ASSIGNED', 'ACCEPTED', 'ARRIVED_PICKUP', 'PICKED_UP', 'ON_DELIVERY', 'ARRIVED_DESTINATION', 'OTP_VERIFIED', 'PROOF_UPLOADED', 'ASSIGNED' => 'is-warning',
            'REJECTED', 'FAILED', 'CANCELLED', 'SUSPENDED', 'OFFLINE', 'INACTIVE' => 'is-danger',
            default => 'is-neutral',
        };
    }
}
