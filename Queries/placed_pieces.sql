CREATE TABLE placed_pieces (
  record_id INT AUTO_INCREMENT PRIMARY KEY,
  game_id INT,
  piece_id INT,
  player_id INT,
  piece_color VARCHAR(10),
  x INT,
  y INT,
  FOREIGN KEY (game_id) REFERENCES game(id)
);