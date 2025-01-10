CREATE TABLE game(
    game_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    player1_id int NOT NULL, 
    player2_id int DEFAULT NULL,
    player3_id int DEFAULT NULL,
    player4_id int DEFAULT NULL,
    game_type enum('2p','4p') DEFAULT NULL,
    player1_ready BOOLEAN DEFAULT NULL,
    player2_ready BOOLEAN DEFAULT NULL,
    player3_ready BOOLEAN DEFAULT NULL,
    player4_ready BOOLEAN DEFAULT NULL
    )



    ALTER TABLE game
ADD COLUMN player1_color ENUM('y', 'r', 'g', 'b', 'yr', 'yg', 'yb', 'gb', 'rb', 'rg'),
ADD COLUMN player2_color ENUM('y', 'r', 'g', 'b', 'yr', 'yg', 'yb', 'gb', 'rb', 'rg'),
ADD COLUMN player3_color ENUM('y', 'r', 'g', 'b', 'yr', 'yg', 'yb', 'gb', 'rb', 'rg'),
ADD COLUMN player4_color ENUM('y', 'r', 'g', 'b', 'yr', 'yg', 'yb', 'gb', 'rb', 'rg');

ALTER TABLE game
ADD COLUMN created timestamp NULL DEFAULT NULL,
ADD COLUMN updated timestamp NULL DEFAULT NULL;