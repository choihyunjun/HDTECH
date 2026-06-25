SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS mold_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mold_db;

CREATE TABLE IF NOT EXISTS molds (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    mold_no         VARCHAR(50)  NOT NULL UNIQUE,
    customer        VARCHAR(100) DEFAULT '',
    car_model       VARCHAR(100) DEFAULT '',
    dm              VARCHAR(100) DEFAULT '',
    part_no         VARCHAR(100) DEFAULT '',
    product_name    VARCHAR(200) DEFAULT '',
    mold_name       VARCHAR(200) DEFAULT '',
    mold_size       VARCHAR(200) DEFAULT '',
    main_equipment  VARCHAR(100) DEFAULT '',
    material        VARCHAR(100) DEFAULT '',
    basis           TEXT,
    mold_material   VARCHAR(100) DEFAULT '',
    expire_date     DATE DEFAULT NULL,
    maker           VARCHAR(200) DEFAULT '',
    made_date       DATE DEFAULT NULL,
    made_cost       BIGINT DEFAULT 0,
    exchange_count  BIGINT DEFAULT 0,
    last_photo_date DATE DEFAULT NULL,
    check_count     BIGINT DEFAULT 0,
    mold_grade      VARCHAR(10)  DEFAULT '',
    note            TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mold_repairs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    mold_id     INT NOT NULL,
    repair_date DATE DEFAULT NULL,
    content     TEXT,
    manager     VARCHAR(100) DEFAULT '',
    cost        BIGINT DEFAULT 0,
    sort_order  INT DEFAULT 0,
    FOREIGN KEY (mold_id) REFERENCES molds(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mold_images (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    mold_id       INT NOT NULL,
    file_path     VARCHAR(500) NOT NULL,
    original_name VARCHAR(500) DEFAULT '',
    sort_order    INT DEFAULT 0,
    FOREIGN KEY (mold_id) REFERENCES molds(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mold_worklogs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    mold_id     INT NOT NULL,
    work_date   DATE DEFAULT NULL,
    equipment   VARCHAR(100) DEFAULT '',
    content     TEXT,
    note        TEXT,
    work_count  BIGINT DEFAULT 0,
    total_count BIGINT DEFAULT 0,
    sort_order  INT DEFAULT 0,
    FOREIGN KEY (mold_id) REFERENCES molds(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mold_sequence (
    prefix   VARCHAR(20) PRIMARY KEY,
    last_no  INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO mold_sequence (prefix, last_no) VALUES ('PS-QC', 0);

-- 샘플 데이터
INSERT IGNORE INTO molds (mold_no, customer, car_model, dm, part_no, product_name, mold_name,
    mold_size, main_equipment, material, mold_material,
    maker, made_date, made_cost, exchange_count, last_photo_date,
    check_count, mold_grade, note)
VALUES (
    'PS-QC-0001', '만도', 'DM', 'DM', 'DM-089-4222',
    '볼트', '로링금형', 'M4.2×16 105/90-45-24',
    '35 TON', 'PS-45', 'SKD61',
    '한일금형', '2014-07-18', 9000000, 1000000, '2014-07-18',
    1000, 'A', '특기사항입니다.'
);

INSERT IGNORE INTO mold_repairs (mold_id, repair_date, content, manager, cost)
VALUES (1, '2014-07-18', '세척', '홍길동', 50000);

INSERT IGNORE INTO mold_worklogs (mold_id, work_date, equipment, content, note, work_count, total_count)
VALUES (1, '2014-07-18', '1호기', '제품생산', '비고입니다.', 1000, 1000);
