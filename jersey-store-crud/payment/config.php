<?php
// Payment gateway configuration.
// eSewa UAT credentials are public test credentials from the official documentation.

const ESEWA_ENV = 'test';
const ESEWA_PRODUCT_CODE = 'EPAYTEST';
const ESEWA_SECRET_KEY = '8gBm/:&EnhH.1/q';


function site_url(string $path = ''): string {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') == '443');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/payment/'))), '/');
    if ($base === '.' || $base === '/') $base = '';
    return $scheme . '://' . $host . $base . '/' . ltrim($path, '/');
}

function esewa_form_url(): string {
    return ESEWA_ENV === 'production'
        ? 'https://epay.esewa.com.np/api/epay/main/v2/form'
        : 'https://rc-epay.esewa.com.np/api/epay/main/v2/form';
}

function esewa_status_url(): string {
    return ESEWA_ENV === 'production'
        ? 'https://epay.esewa.com.np/api/epay/transaction/status/'
        : 'https://rc.esewa.com.np/api/epay/transaction/status/';
}

