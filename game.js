class Game {
    constructor(gameId, playerId, username, boardId) {
        this.gameId = gameId;
        this.playerId = playerId;
        this.username = username;
        this.boardId = boardId;
        this.board = null; 
        this.playerColors = {};
        this.p1id = null;
        this.p2id = null;
        this.currentPlayerId = null;
        this.currentTurn = null;
        let selectedPieceId = null;
        this.placedPieces = []; // Array to store placed pieces (e.g., { pieceId: 1, position: { x: 5, y: 5 } }) 
        this.boardSelectedCoordinates = []; // Array to store board selected coordinates (e.g., [ [0, 0], [0, 1], ... ])
    }
  
    init() {
        console.log(this.boardId, this.gameId, this.playerId, this.username);
        console.log("Game initialized.");
        this.loadBoard(); // Fetch initial board data from server
    }

    reLoadBoard() {
        $.ajax({
            url: 'board_transactions.php', 
            type: 'POST',
            data: { 
              action: 'load', 
              gameId: this.gameId,
              boardId: this.boardId
            },
            success: (response) => {
                response = JSON.parse(response);
              if (response.status === 'success') {
                console.log("Board loaded:", response.board);
                this.board = response.board; // Store the game board
                this.renderBoard(); // Render the game board after loading
              } else {
                console.error("Error loading board:", response.message);
                // Handle the error
              }
            },
            error: () => {
              console.error("Error loading board from server.");
              // Handle the error
            }
          });
    }
  
    loadBoard() {
      $.ajax({
        url: 'board_transactions.php', 
        type: 'POST',
        data: { 
          action: 'load', 
          gameId: this.gameId,
          boardId: this.boardId
        },
        success: (response) => {
            response = JSON.parse(response);
          if (response.status === 'success') {
            console.log("Board loaded:", response.board);
            this.board = response.board; // Store the game board
            this.loadPlayerColors(); // Fetch player colors from database
            this.renderBoard(); // Render the game board after loading
          } else {
            console.error("Error loading board:", response.message);
            // Handle the error
          }
        },
        error: () => {
          console.error("Error loading board from server.");
          // Handle the error
        }
      });
    }

    loadPlayerColors() {
        $.ajax({
            url: 'board_transactions.php', 
            type: 'POST',
            data: {
                action: 'loadPlayerColors',
                gameId: this.gameId
            },
            success: (response) => {
                response = JSON.parse(response);
                if (response.status === 'success') {
                    this.p1id = response.player1_id;
                    this.p2id = response.player2_id;
                    this.playerColors = {
                        [this.p1id]: [response.player1_color, response.player3_color],
                        [this.p2id]: [response.player2_color, response.player4_color]
                      };
                    this.currentPlayerId = this.p1id; // Assuming player 1 starts
                    this.currentTurn = 1; // Assuming player 1 starts
                    this.renderStats();
                console.log("Player colors loaded - P1: ", this.playerColors[this.p1id], "P2: ", this.playerColors[this.p2id]);
                } else {
                console.error("Error loading player colors:", response.message);
                // Handle the error (e.g., display a message to the user)
                }
            },
            error: () => {
                console.error("Error loading player colors from server.");
                // Handle the error
            }
        });
    }
  
    renderBoard() {
        const boardContainer = document.getElementById('gameBoard');
        boardContainer.innerHTML = ''; // Clear existing content
      
        const table = document.createElement('table');
      
        for (let row of this.board) {
          const tableRow = document.createElement('tr');
          for (let cell of row) {
            const tableCell = document.createElement('td');
            tableCell.classList.add('board-cell'); 

            const playerColor = cell; // Cell value represents player Color

            switch (playerColor) {
                case 'b': 
                  tableCell.style.backgroundColor = 'blue'; 
                  break;
                case 'r':
                  tableCell.style.backgroundColor = 'red'; 
                  break;
                case 'g':
                  tableCell.style.backgroundColor = 'green'; 
                  break;
                case 'y':
                  tableCell.style.backgroundColor = 'yellow'; 
                  break;
                default:
                  tableCell.style.backgroundColor = 'white'; // Default color for empty cells
              }
      
            tableRow.appendChild(tableCell);
          }
          table.appendChild(tableRow);
        }
      
        boardContainer.appendChild(table); 
      }

      renderStats() {
        const statsContainer = document.getElementById('gameStatus');
        const playerColorsContainer = document.getElementById('gamePlayers');
        statsContainer.innerHTML = ''; // Clear existing content
        playerColorsContainer.innerHTML = ''; // Clear existing content

        // console.log("Current Player: ", this.currentPlayerId, "Current Color: ", this.currentTurn);

        let currentPlayer;
        let currentRole;
        let currentPID;
        let currentColor;
        let currentColorNum;

        if (this.currentPlayerId === this.p1id) {
            currentPlayer = 0;
            currentRole = 'Player 1';
            currentPID = this.p1id;
        } else if (this.currentPlayerId === this.p2id) {
            currentPlayer = 1;
            currentRole = 'Player 2';
            currentPID = this.p2id;
        }

        if (this.currentTurn === 1) {
            currentColor = this.playerColors[this.currentPlayerId][0];
        } else {
            currentColor = this.playerColors[this.currentPlayerId][1];
        }

        // Get color name from code
        const colorNames = {
            b: 'Blue',
            r: 'Red',
            g: 'Green',
            y: 'Yellow'
        };


        statsContainer.innerHTML = `
            <h3>Current Player: ${currentPID} (${currentRole}) </h3>
            <p>Current Color: ${colorNames[currentColor] || 'Unknown'}</p>
        `;

        playerColorsContainer.innerHTML = `
            <h3>Player 1 Colors: </h3> <span> ${colorNames[this.playerColors[this.p1id][0]] || 'Unknown'} and ${colorNames[this.playerColors[this.p1id][1]] || 'Unknown'}</span>
            <h3>Player 2 Colors: </h3> <span> ${colorNames[this.playerColors[this.p2id][0]] || 'Unknown'} and ${colorNames[this.playerColors[this.p2id][1]] || 'Unknown'}</span>
        `;
        }
      

      addPiece(coordinates, pieceId, playerId, gameId, boardId) {
        let currentColor = null;

        // Validate player ID and current player
        if (playerId != this.currentPlayerId) {
          console.error("It's not your turn!");
          return;
        }

        // Validate Available piece ID
        let validPiece = this.isValidPieceId(pieceId, playerId, gameId);

        if (validPiece === false) { // Check for strict equality to false
          console.error("Invalid piece ID.");
          return;
        } 

        // Validate coordinates
        if (!this.isValidCoordinates(coordinates, boardId, gameId, pieceId)) {
            console.error("Invalid coordinates.");
            return;
        }

        console.log('currentTurn: ', this.currentTurn == 1);

        if (this.currentTurn === 1){
          currentColor = this.playerColors[playerId][0];
          console.log('currentColor: ', currentColor);
        } else if (this.currentTurn === 2){
          currentColor = this.playerColors[playerId][1];
          console.log('currentColor: ', currentColor);
        }

      
        // // Check if the piece can be placed on the board according to the "One Color per Side" rule
        // if (!this.isValidPlacement(pieceId, position)) {
        //   console.error("Invalid placement. Please follow the color and side rules.");
        //   return;
        // }
      
        // Update the board
        // const pieceCoordinates = this.getPieceCoordinates(pieceId); 
        
        // this.getPieceCoordinates(pieceId)
        //     .then(pieceCoordinates => { 
        //       console.log(pieceCoordinates);
        //         for (const coordinate of pieceCoordinates) {
        //             const x = coordinate[0];
        //             const y = coordinate[1];
        //             const currentPlayerColors = this.playerColors[this.currentPlayerId]; 

        //             if (this.currentTurn === 1) { 
        //                 const currentColor = currentPlayerColors[0];
        //                 this.board[y][x] = currentColor; 
        //             } else if (this.currentTurn === 2) { 
        //                 const currentColor = currentPlayerColors[1];
        //                 this.board[y][x] = currentColor;
        //             }
        //         }
        //       })
        //     .catch(error => {
        //       console.error("Error fetching piece coordinates:", error);
        //       // Handle the error here (e.g., display an error message to the user)
        //       return; // Exit the addPiece() function if there's an error
        //     });


        // Update the board on the server
        this.updateBoardOnServer(gameId, boardId, coordinates, pieceId, playerId, currentColor); 

        // Fetch and update placed pieces from the database
        this.fetchAndRenderPlacedPieces(); 

        // // Advance to the next turn
        this.nextPlayer();

        // Render the updated board
        this.renderBoard();

        return true;
      }

      nextPlayer() {
        
        if (this.currentTurn === 1) {
          this.currentTurn = 2; 
        } else if (this.currentTurn === 2) {
            if (this.currentPlayerId === this.p1id) {
                this.currentPlayerId = this.p2id;
                this.currentTurn = 1; 
            } else {
                this.currentPlayerId = this.p1id;
                this.currentTurn = 1; 
            }
        }
      }


      updateBoardOnServer(gameId, boardId, coordinates, pieceId, playerId, currentColor) {
        $.ajax({
          url: 'board_transactions.php', 
          type: 'POST',
          data: { 
            action: 'update', 
            gameId: gameId, 
            boardId: boardId, 
            coordinates: coordinates,
            pieceId: pieceId,
            playerId: playerId,
            currentColor: currentColor
          },
          success: (response) => {
            response = JSON.parse(response);
            console.log("updateBoardOnServer", response);
            if (response.status === 'success') {
              console.log("Board updated on server");
            } else {
              console.error("Error updating board on server:", response.message);
            }
          },
          error: () => {
            console.error("Error updating board on server.");
          }
        });
      }

      fetchAndRenderPlacedPieces() {
        $.ajax({
          url: 'board_transactions.php',
          type: 'POST',
          data: { 
            action: 'getPlacedPieces', 
            gameId: this.gameId 
          },
          dataType: 'json',
          success: (response) => {
            if (response.status === 'success') {
              this.placedPieces = response.placedPieces; // Update placedPieces array with data from server
              this.renderBoard(); // Re-render the board to reflect the updated pieces
            } else {
              console.error("Error fetching placed pieces:", response.message);
            }
          },
          error: () => {
            console.error("Error fetching placed pieces.");
          }
        });
      }

    //   isValidPlacement(pieceId, position) {
    //     const pieceCoordinates = this.getPieceCoordinates(pieceId); 
    //     const playerColor = this.playerColors[this.currentPlayerId][this.currentTurn - 1]; 
      
    //     // 1. Check for first piece placement
    //     if (!this.isFirstPiecePlacementValid(pieceCoordinates, position)) { 
    //         return false;
    //     }
    //     // 2. Check for corner adjacency with existing pieces
    //     if (!this.hasCornerAdjacency(pieceCoordinates, position)) {
    //         return false;
    //     }
      
    //     // 3. Check for no side contact with own color pieces
    //     if (this.hasSideContactWithOwnColor(pieceCoordinates, position, playerColor)) {
    //         return false;
    //       }
        
      
    //     return true;
    //   }

    //   isFirstPiecePlacementValid(playerId, pieceCoordinates, position) {
    //     const x = position.x;
    //     const y = position.y;
    //     const playerColor = this.playerColors[this.currentPlayerId][this.currentTurn - 1];
      
    //     // Check if any pieces have been placed
    //     if (this.placedPieces.length === 0) {
    //         // Handle first piece placement for each player
    //         if (this.currentPlayerId === this.p1id) { 
    //             if (playerColor === this.playerColors[0][0]) { // Player 1, first color
    //                 return (x === 0 && y === 0); // Top-left corner
    //             } else { // Player 1, second color
    //                 return (x === this.board[0].length - 1 && y === this.board.length - 1); // Bottom-right corner
    //             }
    //         } else if (this.currentPlayerId === this.p2id) { 
    //             if (playerColor === this.playerColors[1][0]) { // Player 2, first color
    //                 return (x === this.board[0].length - 1 && y === 0); // Top-right corner
    //             } else { // Player 2, second color
    //                 return (x === 0 && y === this.board.length - 1); // Bottom-left corner
    //             }
    //         }
    //     } else {
    //         // Check if the other player has already placed their first piece
    //         const otherPlayerId = this.currentPlayerId === this.p1id ? 1 : 0;
    //         const otherPlayerFirstColor = this.playerColors[otherPlayerId][0]; 
        
    //         if (this.hasPiecesWithColor(otherPlayerFirstColor)) { 
    //           // If the other player has placed their first piece, no restrictions for the current player
    //           return true; 
    //         } else {
    //           // If the other player hasn't placed their first piece, 
    //           // the current player must also place their first piece in the correct corner
    //             if (this.currentPlayerId === this.p1id) { 
    //                 if (playerColor === this.playerColors[0][0]) { // Player 1, first color
    //                     return (x === 0 && y === 0); // Top-left corner
    //                 } else { // Player 1, second color
    //                     return (x === this.board[0].length - 1 && y === this.board.length - 1); // Bottom-right corner
    //                 }
    //             } else if (this.currentPlayerId === this.p2id) { 
    //                 if (playerColor === this.playerColors[1][0]) { // Player 2, first color
    //                     return (x === this.board[0].length - 1 && y === 0); // Top-right corner
    //                 } else { // Player 2, second color
    //                     return (x === 0 && y === this.board.length - 1); // Bottom-left corner
    //                 }
    //             }
    //         }
    //       }

    //     return false;

    //   }

      hasPiecesWithColor(color) {
        for (let row of this.board) {
          for (let cell of row) {
            if (cell === color) {
              return true; 
            }
          }
        }
        return false;
      }

      loadAvailablePieces() {
        const gameId = $('#global_gameid').val(); // Get gameId from the hidden input

        $.ajax({
            type: 'POST',
            url: 'board_transactions.php', 
            data: { 
                action: 'loadPieces',
                gameId: gameId 
            },
            dataType: 'json',
            success: function(data) {
                const p1color1 = document.getElementById(`p1color1`);
                const p1color2 = document.getElementById(`p1color2`);
                const p2color1 = document.getElementById(`p2color1`);
                const p2color2 = document.getElementById(`p2color2`);
                p1color1.innerHTML = '<h4>Color 1</h4>'; 
                p1color2.innerHTML = '<h4>Color 2</h4>';
                p2color1.innerHTML = '<h4>Color 1</h4>';
                p2color2.innerHTML = '<h4>Color 2</h4>';
                
                let playerContainer = null;
                let pieceHeaders = null;

                data.pieces.forEach(piece => {
                    if (piece.player_num === 'p1') {
                        playerContainer = p1color1;
                        pieceHeaders = "Player 1 Color 1 Pieces";
                    } else if (piece.player_num === 'p2') {
                        playerContainer = p2color1;
                        pieceHeaders = "Player 2 Color 1 Pieces";
                    } else if (piece.player_num === 'p3') {
                        playerContainer = p1color2;
                        pieceHeaders = "Player 1 Color 2 Pieces";
                    } else if (piece.player_num === 'p4') {
                        playerContainer = p2color2;
                        pieceHeaders = "Player 2 Color 2 Pieces";
                    } else {
                        console.error('Invalid player number:', piece.player_num);
                        return;
                    }
                    const pieceElement = document.createElement('p');
                    pieceElement.classList.add('piece'); 
                    pieceElement.innerHTML = piece.available_piece_id + " - " + piece.piece_name; 
    
                    playerContainer.appendChild(pieceElement);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading pieces:', error);
            }
        });
    }

    isValidPieceId(pieceId, playerId, gameId) {
      return new Promise((resolve, reject) => {
        let colorNum = '';
        let pieceValidBool = false;

        if(this.currentPlayerId == this.p1id) {
          // console.log('A');
            if(this.currentTurn == 1) {
              // console.log('A1');
                colorNum = 'p1';
            } else {
              // console.log('A2');
                colorNum = 'p3';
            }
        } else {
          // console.log('B');
            if(this.currentTurn == 1) {
              // console.log('B1');
                colorNum = 'p2';
            } else {   
              // console.log('B2');
                colorNum = 'p4';
            }
        }
        
        // console.log("Piece ID: ", pieceId, "Player ID: ", playerId, "Game ID: ", gameId, "Color Num: ", colorNum);

        $.ajax({
            type: 'POST',
            url: 'board_transactions.php', 
            data: { 
                action: 'pieceValidation',
                gameId: gameId,
                playerId: playerId,
                colorNum: colorNum
            },
            dataType: 'json',
            success: function(data) {
                data.pieces.forEach(piece => {
                    if (piece.available_piece_id == pieceId) {
                        pieceValidBool = true;
                    }
                });
                if (pieceValidBool) {
                    console.log("Piece is valid");
                    resolve(true);
                } else {
                    alert("Piece is invalid");
                    reject(false);
                }
            },
            error: function(error) {
                alert('Error loading pieces:', error.message);
                reject(false);
            }
        });
      });
    }

    isValidCoordinates(coordinates, boardId, gameId, pieceId) {
      return new Promise((resolve, reject) => {
      console.log('TEST', JSON.stringify(coordinates), boardId, gameId, pieceId);
        $.ajax({
            type: 'POST',
            url: 'board_transactions.php',
            data: { 
                action: 'validateCoordinates', 
                gameId: gameId,
                boardId: boardId,
                coordinates: JSON.stringify(coordinates), // Send coordinates as JSON string
                pieceId: pieceId
            },
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                  resolve(true); // Coordinates are valid
                } else {
                    console.error(data.message); 
                    alert("Coordinates are invalid");
                    reject(false);
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                console.error('Error validating coordinates:', error);
                reject(error);
            }
        });
      });
    }    
    
    getPieceCoordinates(pieceId) {
        return new Promise((resolve, reject) => { 
            $.ajax({
                type: 'POST',
                url: 'board_transactions.php',
                data: { 
                    action: 'getPieceCoordinates', 
                    pieceId: pieceId
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        resolve(data.pieceCoordinates); // Return the array of coordinates
                    } else {
                        console.error('Error fetching piece coordinates:', data.message);
                        reject(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error validating coordinates:', error);
                    reject(error);
                }
            });
        });
    }

    hasAvailableMoves(gameId, playerId) {
        // Check if the current player has any available moves
        // (i.e., if there are any available pieces that can be placed on the board)
        // Return true if the player has available moves, and false otherwise
        $.ajax({
            type: 'POST',
            url: 'board_transactions.php',
            data: { 
                action: 'hasAvailableMoves', 
                gameId: gameId,
                playerId: playerId
            },
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    return true; // Player has available moves
                } else {
                    return false; // Player has no available moves
                }
            },
            error: function(xhr, status, error) {
                console.error('Error validating coordinates:', error);
            }
        });
    }

    endingState(gameId, winner) {
      return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: 'end_game_functions.php',
            data: { 
                action: 'endingState', 
                gameId: gameId,
                winner: winner
            },
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    resolve(true); // Ending state updated
                } else {
                    reject(false); // Ending state not updated
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating ending state:', error);
                reject(false); 
            }
        });
      });
    }

    
    calculateAndDisplayScores(gameId) {
      $.ajax({
        type: 'POST',
        url: 'board_transactions.php',
        data: { 
          action: 'calculateScores',
          gameId: gameId 
        },
        dataType: 'json',
        success: (data) => {
          if (data.status === 'success') {
            const playerScores = data.playerScores; 
            // Initialize an array to store total scores for each player
            const userTotals = [0, 0]; // Assuming two players

            let possibleWinnerID = [0, 0];

            // Calculate total score for each player
            playerScores.forEach(playerScore => {
              if (playerScore.player_num === 'p1' || playerScore.player_num === 'p3') { 
                userTotals[0] += playerScore.score; // Add score to player 1's total
                possibleWinnerID[0] = playerScore.player_id;
              } else if (playerScore.player_num === 'p2' || playerScore.player_num === 'p4') { 
                userTotals[1] += playerScore.score; // Add score to player 2's total
                possibleWinnerID[1] = playerScore.player_id;
              }
            });

            let winningPlayerID = 0;

            // Determine the winner (player with the highest score)
            let winningPlayer = 0; // Assume draw initially
            if (userTotals[1] > userTotals[0]) {
              winningPlayerID = possibleWinnerID[1];
              winningPlayer = 2;
            } else if (userTotals[1] < userTotals[0]) { 
              winningPlayerID = possibleWinnerID[0];
              winningPlayer = 1;
            }
    
            const player1ScoresElement = document.getElementById('player1Scores');
            const player2ScoresElement = document.getElementById('player2Scores');
            const winnerMessageElement = document.getElementById('winnerMessage');
            const winnerIDElement = document.getElementById('winnerID');

            player1ScoresElement.innerHTML = `Player 1 Score: ${userTotals[0]}`;
            player2ScoresElement.innerHTML = `Player 2 Score: ${userTotals[1]}`;

            if (winningPlayer == 1) {
              winnerMessageElement.innerHTML = "Player 1 Wins!";
            } else if (winningPlayer == 2) {
              winnerMessageElement.innerHTML = "Player 2 Wins!";
            } else {
              winnerMessageElement.innerHTML = "It's a Draw!";
            }

            if (winningPlayerID == 0) {
              winnerIDElement.innerHTML = null;
            } else {
              winnerIDElement.innerHTML = winningPlayerID;
            }
    
          } else {
            console.error("Error calculating scores:", data.message);
          }
        },
        error: () => {
          console.error("Error calculating scores.");
        }
      });
    }

    returnEnd(gameId) {
    //   if (confirm("Are you sure you want to return to the lobby? You will lose the current game.")) {
    
        $.ajax({
          type: 'POST',
          url: 'end_game_functions.php',
          data: { 
            action: 'returnEnd', 
              gameId: gameId
          },
          // dataType: 'json',
          success: function(data) {
            console.log(data);
            if (data.status == 'success') { 
              console.log('Game ended successfully.');
              window.location.href = 'lobby.php'; 
            } else {
              console.error('Error ending the game:', data.message);
              // Handle error, e.g., display an error message to the user
            }
          },
          error: function(jqXHR, textStatus, error) {
            console.error('Error ending the game:', error); // Use errorThrown instead of 'error'
            // Handle error, e.g., display an error message to the user
          }
        });
      }
    // }    
        // $.ajax({
        //     url: 'board_transactions.php', 
        //     type: 'POST',
        //     data: { 
        //       action: 'loadPieces', 
        //       gameId: this.gameId, 
        //       playerNum: this.playerNum, 
        //       playerId: this.playerId
        //     },
        //   success: function(data) {
        //     const playerPiecesContainer = document.getElementById(`player${playerId}Pieces`); 
        //     playerPiecesContainer.innerHTML = ''; 
      
        //     data.forEach(piece => {
        //       const pieceElement = document.createElement('div');
        //       pieceElement.classList.add('piece'); 
        //       pieceElement.innerHTML = piece.piece_name; 
        //       playerPiecesContainer.appendChild(pieceElement);
        //     });
        //   },
        //   error: function(xhr, status, error) {
        //     console.error('Error loading pieces:', error);
        //   }
        // });
  }