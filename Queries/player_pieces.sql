CREATE TABLE player_pieces (
    record_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    game_id INT,
    player_num ENUM('p1', 'p2', 'p3', 'p4'),
    user_id INT,
    available_piece_id INT,
    FOREIGN KEY (game_id) REFERENCES game(game_id),
    FOREIGN KEY (available_piece_id) REFERENCES pieces(piece_id)
);