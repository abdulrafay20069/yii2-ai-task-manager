CREATE TABLE IF NOT EXISTS `task` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT,
    `status` VARCHAR(20) NOT NULL DEFAULT 'todo',
    `priority` TINYINT NOT NULL DEFAULT 2,
    `due_date` DATE NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);
