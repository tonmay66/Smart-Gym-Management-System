<?php
declare(strict_types=1);

function ensure_logged_in(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  if (empty($_SESSION['auth'])) {
    header("Location: /gymm/public/login");
    exit;
  }
}

function ensure_role(string $role): void {
  ensure_logged_in();
  if (($_SESSION['auth']['role'] ?? '') !== $role) {
    http_response_code(403);
    echo "403 Forbidden";
    exit;
  }
}
