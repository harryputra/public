<?php
require_once __DIR__ . '/config/config.php';
if (isLoggedIn()) {
    redirect(dashboardUrl());
}
redirect(BASE_URL . 'login.php');
