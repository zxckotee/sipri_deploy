-- Databases and users for SIPRI + app.sipri

CREATE DATABASE IF NOT EXISTS sipri_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS app_sipri CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'sipri'@'%' IDENTIFIED BY 'sipri';
GRANT ALL PRIVILEGES ON sipri_crm.* TO 'sipri'@'%';

CREATE USER IF NOT EXISTS 'app_sipri'@'%' IDENTIFIED BY 'app_sipri';
GRANT ALL PRIVILEGES ON app_sipri.* TO 'app_sipri'@'%';

FLUSH PRIVILEGES;


