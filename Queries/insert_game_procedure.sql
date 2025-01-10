DELIMITER $$
CREATE PROCEDURE insert_game(IN p_player_id INT, IN p_game_type VARCHAR(20), IN p_player_ready BOOLEAN, OUT p_game_id INT)
BEGIN
  INSERT INTO game (player1_id, game_type, player1_ready) VALUES (p_player_id, p_game_type, p_player_ready);
  SET p_game_id = LAST_INSERT_ID();
END$$
DELIMITER ;