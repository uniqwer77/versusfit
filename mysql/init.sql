CREATE DATABASE IF NOT EXISTS versusfit_main
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

GRANT ALL ON versusfit_main.* TO 'student'@'%';

FLUSH PRIVILEGES;