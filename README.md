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

## Alpha03 (Possible Enhancements - Additions)
* Game.php - Page Completion
* I will transfer Login, Register in my Index page
* I will add User Login Status and Logout in every page


