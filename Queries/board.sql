CREATE TABLE board(
    board_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    game_id int NOT NULL,
    board text,
    created timestamp NULL DEFAULT NULL,
    updated timestamp NULL DEFAULT NULL,
    FOREIGN KEY (game_id) REFERENCES game(game_id)
    )