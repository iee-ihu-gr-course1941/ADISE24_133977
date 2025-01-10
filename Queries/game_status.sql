CREATE TABLE game_status (
    status_id int NOT NULL AUTO_INCREMENT PRIMARY, 
    game_id int NOT NULL,
    gstatus enum('not active','initialized','started','ended','aborted') NOT NULL DEFAULT 'not active',
    p_turn enum('p1','p2','p3','p4') DEFAULT NULL,
    result enum('p1','p2','p3','p4','d') DEFAULT NULL,
    created timestamp NULL DEFAULT NULL,
    updated timestamp NULL DEFAULT NULL
)
