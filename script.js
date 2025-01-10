function checkSession() { // This function is checking if the user is logged in 
  const isLoggedIn = document.getElementById('isLoggedIn').value;
  const UserName = document.getElementById('usernm').value;

  if (isLoggedIn === 'true') {
    document.getElementById('userStatus').textContent = 'Welcome, ' + UserName;
    document.getElementById('logout-btn').style.display = 'block';
  } else {
    document.getElementById('userStatus').textContent = 'You are not logged in.';
  }
}


if (window.location.href.indexOf('lobby.php') !== -1) {
document.getElementById('player1-ready').addEventListener('click', () => {
  const player1Color = document.getElementById('player1-color').value;
  const player3Color = document.getElementById('player3-color').value;
  const p1ColorOut = fetchGameInfo(player1Color);
  const p3ColorOut = fetchGameInfo(player3Color);
  const gameId = document.getElementById('global_gameid').value;
  const playerId = document.getElementById('global_playerid').value;
  const player1id = document.getElementById('player1id_hidden').value;

  console.log(player1Color, player3Color, gameId, playerId, player1id);

  if (validateColors(player1Color,player3Color)) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_player_status.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    const data = new URLSearchParams({
      gameId: gameId,
      playerId: playerId,
      player1id: player1id,
      player1Color: player1Color,
      player3Color: player3Color
    });

    xhr.send(data.toString());

    xhr.onload = function() {
      if (xhr.status === 200) {
        // Handle successful response
        alert(xhr.responseText);
        // document.getElementById('player1-choice').textContent = p1ColorOut + " & " + p3ColorOut;
      } else {
        // Handle error
        console.error('Request failed.  Returned status of ' + xhr.status);
        alert(xhr.responseText);
      }
    };

    // Send an AJAX request to update the database
    // fetch('update_player_status.php', {
    //   method: 'POST',
    //   headers: {
    //     'Content-Type': 'application/json'
    //   },
    //   body: JSON.stringify({
    //     gameId: gameId,
    //     playerId: playerId,
    //     player1id: player1id,
    //     player1Color: player1Color,
    //     player3Color: player3Color
    //   })
    // })
    // .then(response => response.text())
    // .then(data => {
    //   if (data === 'success') {
    //     // Handle successful update
    //     alert('Your choices have been saved.');
    //     document.getElementById('player1-choice').textContent = player1Color + " & " + player3Color;
    //   } else {
    //     // Handle error
    //     alert(data);
    //     // alert('Error saving choices. ' + data.message);
    //   }
    // });
  } else {
    alert('Please select different colors for Player 1.');
  }
});



document.getElementById('player2-ready').addEventListener('click', () => {
  const player2Color = document.getElementById('player2-color').value;
  const player4Color = document.getElementById('player4-color').value;
  const p2ColorOut = fetchGameInfo(player2Color);
  const p4ColorOut = fetchGameInfo(player4Color);
  const gameId = document.getElementById('global_gameid').value;
  const playerId = document.getElementById('global_playerid').value;
  const player1id = document.getElementById('player1id_hidden').value;

  if (validateColors(player2Color,player4Color)) {

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_player_status.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    const data = new URLSearchParams({
      gameId: gameId,
      playerId: playerId,
      player1id: player1id,
      player2Color: player2Color,
      player4Color: player4Color
    });

    xhr.send(data.toString());

    xhr.onload = function() {
      if (xhr.status === 200) {
        // Handle successful response
        alert(xhr.responseText);
        // document.getElementById('player2-choice').textContent = p2ColorOut + " & " + p4ColorOut;
      } else {
        // Handle error
        console.error('Request failed.  Returned status of ' + xhr.status);
      }
    };
  } else {
    alert('Please select different colors for Player 2.');
  }
});
}


function validateColors(color1, color2){  // Check if both colors per user are selected and different
  return color1 !== color2 && color1 !== '' && color2 !== '';
}



function fetchGameInfo(color){ // Returns the full name of every color
  const inputColor = color;

  if (inputColor == 'r'){
    return 'Red';
  } else if (inputColor == 'b'){
    return 'Blue';
  } else if (inputColor == 'g'){
    return 'Green';
  } else if (inputColor == 'y'){
    return 'Yellow';
  } else {
    return 'No Color';
  }
}




function updatePlayerColors (){ // Will update player color status inside lobby.php
  // Check if the current page is lobby.php
  if (window.location.href.indexOf('lobby.php') !== -1) {
    const gameId = document.getElementById('global_gameid').value;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'updateColors.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    const data = new URLSearchParams({
      gameId: gameId
    });

    xhr.send(data.toString());

    xhr.onload = function() {
      if (xhr.status === 200) {
        const responseData = JSON.parse(xhr.responseText)[0];
        if (responseData.error) {
          console.error(responseData.error);
          // Handle error message from PHP
        } else {
          // Update player choices with retrieved colors
          document.getElementById('player1-choice').textContent = fetchGameInfo(responseData.player1Color) + " & " + fetchGameInfo(responseData.player3Color);
          document.getElementById('player2-choice').textContent = fetchGameInfo(responseData.player2Color) + " & " + fetchGameInfo(responseData.player4Color);
        }
      } else {
        console.error('Request failed.  Returned status of ' + xhr.status);
      }
    };
      // Schedule the next execution after 3 second
      setTimeout(updatePlayerColors, 3000);
  }
}

// Start the initial execution
updatePlayerColors();





function checkReadiness (){ // Will activate start game button if both players are ready
  // Check if the current page is lobby.php
  if (window.location.href.indexOf('lobby.php') !== -1) {
    const gameId = document.getElementById('global_gameid').value;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'checkReadiness.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    const data = new URLSearchParams({
      gameId: gameId
    });

    xhr.send(data.toString());

    xhr.onload = function() {
      if (xhr.status === 200) {
        const responseData = JSON.parse(xhr.responseText)[0];
        if (responseData.error) {
          console.error(responseData.error);
          // Handle error message from PHP
        } else {
          // Check if players are ready
          if(responseData.player1ready == '1' && responseData.player2ready == '1'){
            document.getElementById('startGameButton').disabled = false;
          } 
        }
      } else {
        console.error('Request failed.  Returned status of ' + xhr.status);
      }
    };

      // Schedule the next execution after 2 second
      setTimeout(checkReadiness, 2000);
  }
}

// Start the initial execution
checkReadiness();



// document.getElementById('startGameButton').addEventListener('click', () => { //Redirects players to game based on its id
//   const gameId = document.getElementById('global_gameid').value;
//   // window.location.href = `game.php?game_id=${gameId}`;
//   // Make an AJAX request to start the game
//   fetch('start_game_functions.php', {
//     method: 'POST',
//     headers: {
//         'Content-Type': 'application/x-www-form-urlencoded'
//     },
//     body: new URLSearchParams({
//         'gameId': gameId
//     })
//   })
//   .then(response => {
//       if (response.ok) {
//           fetch('board_transactions.php', {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/x-www-form-urlencoded'
//             },
//             body: new URLSearchParams({
//                 'gameId': gameId,
//                 'action' : 'initialize'
//             })
//           }).then(response => {
//             if (response.ok) {
//               return response.json(); // Parse the response as JSON
//           } else {
//               throw new Error('Error starting the game.'); 
//           }
//           })
//           .then(async data => {
//               if (data.status === 'success') {
//                   // Game started successfully, fetch the initial board state
//                   const boardResponse = await fetch('board_transactions.php', {
//                   method: 'POST',
//                   headers: {
//                     'Content-Type': 'application/x-www-form-urlencoded'
//                   },
//                   body: new URLSearchParams({
//                     'gameId': gameId,
//                     'action': 'load'
//                   })
//                 });
//                 if (boardResponse.ok) {
//                   return boardResponse.json();
//                 } else {
//                   throw new Error('Error loading board.');
//                 }
//               } else {
//                   throw new Error(data.message || 'Error starting the game.');
//               }
//           })
//           .then(boardData => {
//               if (boardData.status === 'success') {
//                   // Process the loaded board data (e.g., display it on the screen)

//                   const board_id = boardData.boardId;

//                   // Game started successfully, redirect to game.php
//                   window.location.href = `game.php?game_id=${gameId}&board_id=${board_id}`; // Redirect to the game page
//               } else {
//                   throw new Error(boardData.message || 'Error loading board.');
//               }
//           })
//           .catch(error => {
//               console.error('Error:', error);
//               // Handle the error (e.g., display an error message to the user)
//           });
//       }
//   })
//   .catch(error => {
//       console.error('Error:', error);
//   });
// });


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




// window.addEventListener('beforeunload', function (event) {    // Tried to build a function that logout player everytime closes tab or window
//   const gameId = document.getElementById('global_gameid').value;
//   if (event.target.location.href !== window.location.href) {
//     return; // Don't trigger the logout process for navigation
//   }
//   event.preventDefault();
//   // Send a request to the server to update the game status
//   const xhr = new XMLHttpRequest();
//   xhr.open('POST', 'logout.php');

//   const data = new URLSearchParams({
//     gameId: gameId
//   });

//   xhr.send(data.toString());

//     // xhr.onload = function() {
//     //   if (xhr.status === 200) {
//     //     // Handle successful response
//     //     alert(xhr.responseText);
//     //   } else {
//     //     // Handle error
//     //     console.error('Request failed.  Returned status of ' + xhr.status);
//     //   }
//     // };

// });


