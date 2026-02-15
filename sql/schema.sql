CREATE DATABASE IF NOT EXISTS flashminds
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE flashminds;

CREATE TABLE IF NOT EXISTS decks (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cards (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    deck_id      INT UNSIGNED NOT NULL,
    front        TEXT NOT NULL,
    back         TEXT NOT NULL,
    box          TINYINT UNSIGNED NOT NULL DEFAULT 1,
    next_review  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (deck_id) REFERENCES decks(id) ON DELETE CASCADE,
    INDEX idx_deck_review (deck_id, next_review),
    INDEX idx_box (box)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
