<?php
$passwords = [
    'student123',
    'teacher123',
    'admin123'
];

foreach ($passwords as $password) {
    echo $password . ': ' . password_hash($password, PASSWORD_DEFAULT) . "\n";
}
?> 