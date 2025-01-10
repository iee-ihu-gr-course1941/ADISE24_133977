# ADISE24_133977
Blokus Game Implementation

## Alpha01 (Released)
First Implementation of Blokus Game (in a local Apache Server)
* **Index.php** -> Only a single choice for rules.
* **Header.php** + **Footer.php** 
* **Rules.php** -> Added buttons for login, register, logout and status text. Also, I will add a text the rules of the game.
* **Game.php** - > Nothing yet, but only a check if the user is logged in before he/she enters in Game
* **Config.php** -> DB Configuration
* **Login.php, Logout.php, Register.php** -> All the forms and the php code app needs for user Login - Register or Logout

Also, a local DB implementation based in MySQL with just users data.
DB Structure for Users is this:
<table>
  <thead>
    <th>Users</th>
  </thead>
  <tbody>
    <td>id (Key, Auto-Implement)</td>
  </tbody>
  <tbody>
    <td>Firstname</td>
  </tbody>
  <tbody>
    <td>Lastname</td>
  </tbody>
  <tbody>
    <td>Email</td>
  </tbody>
  <tbody>
    <td>Username</td>
  </tbody>
  <tbody>
    <td>Passwd (Hash Type Password)</td>
  </tbody>
  <tbody>
    <td>Score</td>
  </tbody>
</table>

## Alpha02 (Released - 28/11/2024)
* Completed: 
  * **Rules.php** (Added game rules. Also, status if a user abort a game)
  * **Lobby.php** (Analysis below)
  * Helpers:
    * **check_game_status.php** (Analysis below)
    * **checkReadiness.php** (Analysis below)
    * **update_player_status.php** (Analysis below)
    * **updateColors.php** (Analysis below)
* In Progress:
  * Game Board Implementation
  * Game Helpers
  * Game Moves
  * Game Design
 
### Lobby Functionality (lobby.php)
The **lobby.php** page serves as the central hub for players to join or create a Blokus Game. Here's a breakdown of its functionalities:

**1. Joining a Game:**

System finds if the player has already joined an existing game's ID (game_id).
If not, finds the first available game that waiting for player (Status -> Not Active) and join the player in that game.
If nothing of the above pass the validation, the system creates a new game and returns the game_id.
Upon successful joining, the player has to choose two of the four colors and declare its readiness.

**2. Player Information:**

The page displays the current player's information, potentially including user ID and color.

**3. Logout Functionality:**

The page provides a mechanism for players to log out of the game session.
Upon logout, the player might be redirected to a login page or disconnected from the game server. 

**Integration with Other Pages:**

The lobby.php page interacts with **check_game_status.php, checkReadiness.php, update_player_status.php** and **updateColors.php** to validate those rules:
  * **check_game_status.php** -> Checks if a game took an aborted status and transfers user to login page
  * **checkReadiness.php** -> Checks if players are ready to proceed to game
  * **update_player_status.php** -> Checks and returns all the colors players choose and updates database
  * **updateColors.php** -> Finds and returns all players colors from Server

### Front-end Technologies:

* **HTML**: The basic structure of the web page.
* **CSS**: Styling the page elements.
* **JavaScript**: Client-side scripting for user interactions, game logic, and network communication.
  * **XMLHttpRequest (XHR):** Asynchronous communication with the server to fetch data or send updates.
  * **AJAX (Asynchronous JavaScript and XML):** A technique that uses XHR to create dynamic web applications without full page reloads.

### Back-end Technologies:

* **Server-Side Language**: PHP
* **Database**: MySQL to store game data, user information, and game states.
* **Web Server**: Apache HTTP Server to serve the web pages and handle requests.

**Version Control**: Git for managing code changes and collaboration.

### New Database Entries:

<table>
  <thead>
    <th>Game</th>
    <th>Game Status</th>
  </thead>
  <tbody>
    <td>game_id (Key, Auto-Implement)</td>
    <td>status_id (Key, Auto-Implement)</td>
  </tbody>
  <tbody>
    <td>player1_id</td>
    <td>game_id</td>
  </tbody>
  <tbody>
    <td>player2_id</td>
    <td>gstatus ENUMERATOR</td>
  </tbody>
  <tbody>
    <td>player3_id</td>
    <td>p_turn ENUMERATOR</td>
  </tbody>
  <tbody>
    <td>player4_id</td>
    <td>result ENUMERATOR</td>
  </tbody>
  <tbody>
    <td>game_type ENUMERATOR</td>
    <td>created TIMESTAMP</td>
  </tbody>
  <tbody>
    <td>player1_ready</td>
    <td></td>
  </tbody>
  <tbody>
    <td>player2_ready</td>
    <td></td>
  </tbody>
  <tbody>
    <td>player3_ready</td>
    <td></td>
  </tbody>
  <tbody>
    <td>player4_ready</td>
    <td></td>
  </tbody>
  <tbody>
    <td>player1_color</td>
    <td></td>
  </tbody>
  <tbody>
    <td>player2_color</td>
    <td></td>
  </tbody>
  <tbody>
    <td>player3_color</td>
    <td></td>
  </tbody>
  <tbody>
    <td>player4_color</td>
    <td></td>
  </tbody>
</table>

## Alpha03 (Released - 10/01/2025)
* Main Game: 
  * **Game.php** (Not Working - Analysis below)
  * Helpers:
    * **board_transactions.php** (Analysis below)
    * **end_game_functions.php** (Analysis below)
    * **start_game_functions.php** (Analysis below)
    * **game.js** (Analysis below)
* Known Bugs:
    * If a player aborts the game the other player needs to refresh to find out
    * Also, they need to logout to join in a new game
    * In the lobby page, needs a refresh to appear the other player's ID

### Game Functionality (game.php)

The code provides the following functionalities:

* Connects to the database to retrieve game data (using PHP).
* Establishes the current player and displays the appropriate UI elements (using PHP).
* Loads available game pieces for each player based on their role (using JavaScript).
* Enables players to select a piece and a position on the game board (using JavaScript).
* Sends the player's move (piece ID and position) to the server for validation and processing (using JavaScript).
* Updates the game board and available pieces based on the server's response (using JavaScript).
* Handles turn management, disabling input for the non-active player (using JavaScript).
* Calculates and displays the winner upon game over (using JavaScript).
* Allows players to exit the game (using JavaScript).

### Game JavaScript (game.js)

Here's a brief overview of the game methods:

* **init()**: This method is likely called to initialize the game. It logs the game ID, player ID, username, and board ID to the console and then calls the **loadBoard** method to fetch the initial game board data.
* **reloadBoard()**: This method is used to reload the game board data from the server. It makes an AJAX request to the **board_transactions.php** script with an action of load and the game ID and board ID as parameters. Upon successful response, it updates the this.board property and calls the **renderBoard** method to update the visual representation of the board.
* **loadBoard()**: This method is similar to **reloadBoard** but might be used specifically for the initial loading of the board data. It follows the same logic as **reloadBoard** to fetch the board data and update the game state.
* **loadPlayerColors()**: This method fetches the player color assignments from the server. It makes an AJAX request to the **board_transactions.php** script with an action of **loadPlayerColors** and the game ID as a parameter. Upon successful response, it stores the player colors in the **this.playerColors** property, sets the currentPlayerId and currentTurn properties based on assumptions (e.g., assuming player 1 starts), and renders the player stats using the renderStats method.
* **renderBoard()**: This method is responsible for creating a visual representation of the game board on the HTML page. It iterates through the this.board array, which contains the board data, and creates a table element with corresponding cells. The background color of each cell is set based on the player color stored in the board data. Finally, the created table is appended to the game board container element on the HTML page.
* **renderStats()**: This method updates the section displaying the current player
* **Add a piece**:

  * This function seems to handle adding a piece to the game board.
  * It performs validations to ensure the player placing the piece is the current player and the piece being placed is valid.
  * It also validates the coordinates where the piece is being placed.
  * If all validations pass, the function updates the board state and advances the game to the next player's turn.
* **Next player**:

  * This function simply switches the current player based on the turn.
* **Update board on server**:

  * This function sends an AJAX request to the server to update the game board state with the newly placed piece.
* **Fetch and render placed pieces**:

  * This function retrieves the current state of the placed pieces from the server and updates the game board visually to reflect the changes.
* **Validate coordinates**: (This functionality seems to be implemented but commented out)

  * This function likely checks if the coordinates provided for placing a piece are valid within the game board boundaries and rules.
* **Has available pieces**:

  * This function seems to check if the current player has any available pieces to place on the board.
* **Ending state**:

  * This function updates the game state to indicate the end of the game and assigns a winner.
* **Calculate and display scores**:

  * This function retrieves the scores for each player from the server and calculates the total score.
It likely also updates the game UI to display the final scores.


### Board Transactions (board_transactions.php)

This code defines functions for managing the game board in a board game. It includes:

* **Board Initialization**:

  * Creates a 20x20 2D array representing the empty board.
  * Stores the initial board state in the database.
* **Board Loading**:

  * Retrieves the current state of the board from the database.
* **Board Updating**:

  * Updates the board state in the database with the latest changes.
* **Player Color Loading**:

  * Retrieves the colors assigned to each player from the database.
* **Placement Validation**:

  * This is a core function that checks the validity of a piece placement.
    * It incorporates several checks:
      * **Bounds Check**: Ensures the placed piece doesn't go beyond the board boundaries.
      * **Adjacency Check**: Verifies if the placed piece is adjacent to at least one of the player's existing pieces.
      * **Overlap Check**: Checks if the placed piece overlaps with any existing pieces on the board.

* **Helper Functions**:

  * **getCurrentPlayerTurn()**: Retrieves the current player's turn.
  * **getCurrentPlayerID()**: Retrieves the IDs of the players.
  * **getPlayerPiecesOnBoard()**: Retrieves the coordinates of the player's existing pieces on the board.
  * **getPlayerPiecesFromDatabase()**: Retrieves the player's pieces from the database.
  * **getPieceCoordinatesFromDatabase()**: Retrieves the coordinates of a specific piece from the database.
  * **hasAdjacentPiece()**: Checks if the placed piece is adjacent to any existing pieces.
  * **hasOverlappingPieces()**: Checks if the placed piece overlaps with any existing pieces.
  * **hasAvailableMoves()**: Checks if the current player has any available moves.
  * **getBoardStateFromDatabase()**: Retrieves the current state of the board from the database.
  * **getPotentialPositions()**: Generates potential positions for placing a piece based on existing player pieces.
  * **getAdjacentCells()**: Returns the coordinates of adjacent cells to a given cell.
  * **placePieceOnBoard()**: Simulates placing a piece on the board and returns the updated board state.

* **Database Interactions**:

  * The code interacts with a database (likely MySQL) to store and retrieve game data:
  * Board initialization, loading, and updating.
  * Player information, including colors.
  * Piece information and placements.

Also, the code handles incoming requests (POST requests) to perform various actions related to the game board. These actions include:

  * **Initializing the board**: Creates a new board for a given game.
  * **Loading the board**: Retrieves the current state of the board from the database.
  * **Updating the board**: Updates the board state after a piece is placed.
  * **Loading player colors**: Retrieves the colors assigned to each player.
  * **Loading available pieces**: Retrieves the pieces available to each player.
  * **Validating piece IDs**: Checks if a given piece ID is valid for the current player.
  * **Validating coordinates**: Checks if the specified coordinates for placing a piece are valid.
  * **Retrieving piece coordinates**: Retrieves the coordinates of a specific piece.
  * **Retrieving placed pieces**: Retrieves the coordinates of all placed pieces on the board.
  * **Calculating scores**: Calculates the scores for each player based on their placed pieces. 

**Request Handling**:

  * The code begins by checking for a POST request and the presence of a gameId.
  * It then uses a series of if-else statements to determine the requested action based on the action parameter.
  * Each action is handled by a corresponding function or a set of database queries.
**Data Handling**:

  * The code receives data from the POST request, such as gameId, boardId, playerId, coordinates, and pieceId.
  * It validates the input data (e.g., checking for numeric values, checking for required parameters).
  * It interacts with the database to retrieve and update game data.
  * It uses JSON encoding to send data back to the client.


 ### Addition in script.js

 ```
 $('#startGameButton').click(function() {
  const gameId = $('#global_gameid').val();

  console.log('Game ID:', gameId);

  $.post('start_game_functions.php', { gameId: gameId })
      .done(function(data) {
        data = JSON.parse(data);
        console.log("Game started:", data);
          if (data.status === 'success') {
              $.post('board_transactions.php', { gameId: gameId, action: 'initialize' })
                  // .done(function(response) {
                      // if (response.status === 'success') {
                      //     return $.post('board_transactions.php', { gameId: gameId, action: 'load' });
                      // } else {
                      //     throw new Error('Inside Error | Error starting the game.');
                      // }
                  // })
                  .done(function(response) {
                    response = JSON.parse(response);
                    console.log("Board Info:", response);
                      if (response.status === 'success') {
                          const board_id = response.board_id;
                          window.location.href = `game.php?game_id=${gameId}&board_id=${board_id}`;
                      } else {
                          throw new Error(response.message || 'Error loading board.');
                      }
                  })
                  .fail(function(jqXHR, textStatus, errorThrown) {
                      throw new Error('(Inner) Error fetching board data: ' + errorThrown);
                  });
          } else if (data.status === 'board-ok') {
              const board_id = data.board_id;
              window.location.href = `game.php?game_id=${gameId}&board_id=${board_id}`;
          } else {
              throw new Error(data.message || '(start_game_functions.php) Error starting the game.');
          }
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
          throw new Error('Error starting the game: ' + errorThrown);
      });
});
```


### start_game_functions.php

This code defines a function **startGame()** and handles a **POST request** to start a game. 
Here's a breakdown:

* **startGame()** Function:

  * Checks if the game with the given gameId exists in the game_status table.
  * If the game exists and its status is already "initialized":
    * Retrieves the board_id for the game.
    * Updates the game status to "started" and sets the initial player turn to "p1".
    * Returns a success message with the board_id.
  * If the game exists but is not initialized, it updates the game status to "initialized".
  * If the game is not found, it returns an error message.
* *POST Request Handling*:

  * Checks if the request is a POST request and if the gameId is provided and valid.
  * Calls the startGame() function to handle the game start logic.
  * Encodes the result of the startGame() function as JSON and sends it back to the client.


### end_game_functions.php


This code defines a function **endGame()** and handles **POST requests** related to ending a game. Here's a breakdown:

**endGame()** Function:

  * Checks if the game with the given gameId exists in the game_status table.
  * If the game exists and its status is already "ended", it returns a message indicating that the game is already ended.
  * If the game exists and is not ended, it updates the game status to "ended".
  * It then updates the result field in the game_status table to record the winner (if provided).
**POST Request Handling**:

  * Checks if the request is a **POST request** and if the gameId is provided and valid.
  * Handles different actions based on the action parameter:
    * **endingState**:
      * Retrieves the player IDs associated with the game.
      * Validates the provided winner ID to ensure it matches one of the player IDs or is 'd' for a draw.
      * Calls the endGame() function to update the game status.
      * Returns the result of the endGame() function as JSON.
    * **returnEnd**:
      * Updates the game status to "aborted" to indicate that the game was prematurely ended.
      * Returns a success or error message based on the update result.
