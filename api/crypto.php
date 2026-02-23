<?php
define('HMAC_KEY', 'VoteSecure');

define('AES_KEY', hex2bin('0123456789ABCDEF0123456789ABCDEF'));
define('AES_METHOD', 'AES-256-GCM');

function hmac_hash($data) {
  return hash_hmac('sha256', $data, HMAC_KEY);
}

function aes_encrypt($data) {
  $iv = random_bytes(12);
  $cipher = openssl_encrypt(
    $data,
    AES_METHOD,
    AES_KEY,
    OPENSSL_RAW_DATA,
    $iv,
    $tag
  );
  return base64_encode($iv.$tag.$cipher);
}

function aes_decrypt($data) {
  $data = base64_decode($data);
  $iv = substr($data, 0, 12);
  $tag = substr($data, 12, 16);
  $cipher = substr($data, 28);
  return openssl_decrypt(
    $cipher,
    AES_METHOD,
    AES_KEY,
    OPENSSL_RAW_DATA,
    $iv,
    $tag
  );
}

// Alias para compatibilidad
function encrypt_data($data) {
  return aes_encrypt($data);
}

function decrypt_data($data) {
  return aes_decrypt($data);
}

