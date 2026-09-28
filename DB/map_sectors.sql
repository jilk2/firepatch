CREATE TABLE map_sectors (
    sector_number TINYINT UNSIGNED PRIMARY KEY,
    state VARCHAR(32) NOT NULL,
    CHECK (sector_number BETWEEN 1 AND 36)
);