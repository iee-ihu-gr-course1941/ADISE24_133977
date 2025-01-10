DELIMITER $$

CREATE PROCEDURE insert_initial_player_pieces(IN p_game_id INT, IN p_player_num ENUM('p1', 'p2', 'p3', 'p4'), IN p_user_id INT)
BEGIN
    DECLARE v_piece_id INT;
    DECLARE v_max_pieces INT DEFAULT 21; 

    SET v_piece_id = 1;

    WHILE v_piece_id <= v_max_pieces DO
        INSERT INTO player_pieces (game_id, player_num, user_id, available_piece_id) 
        VALUES (p_game_id, p_player_num, p_user_id, v_piece_id);
        SET v_piece_id = v_piece_id + 1;
    END WHILE;
END $$

DELIMITER ;