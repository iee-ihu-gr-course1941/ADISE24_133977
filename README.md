# ADISE24_133977
Blokus Game Implementation

## Alpha01
First Implementation of Blokus Game (in a local Apache Server)
* Index.php -> Only a single choice for rules. 
* Rules.php -> Added buttons for login, register, logout and status text. Also, I will add a text the rules of the game.
* Game.php - > Nothing yet, but only a check if the user is logged in before he/she enters in Game
* Config.php -> DB Configuration
* Login.php, Logout.php, Register.php -> All the forms and the php code app needs for user Login - Register or Logout

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

## Alpha02 (Future Enhancements)
* I will transfer Login, Register in my Index page
* I will add User Login Status and Logout in every page
* I will add Extended text for Rules
* I will add tables GameData, Session and Moves in my Database for proper saves

  
