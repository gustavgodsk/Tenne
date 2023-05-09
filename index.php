<!DOCTYPE html>
<html translate="no">
<head>
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
  <meta charset="utf-8" lang="en">
  <meta name="google" content="notranslate">
</head>
<body>

<div class="menu-screen">
  <div class="menu-left-div">
    <img src="images/logo.png" href="">
    <div class="menu-button-div">
      <button id="spil-button">Spil</button>
      <button id="frispil-button">Frispil</button>
    </div>
  </div>
  <div class="menu-right-div">
    <div class="ski-menu">
      <div class="ski-titel-div">
        <button class="ski-swap-btn" id="ski-titel-left-arrow">&#9654;</button>
        <p class="ski-navn">Pisteski</p>
        <button class="ski-swap-btn">&#9654;</button>
      </div>
      <div class="ski-menu-img-div">
      <div class="ski-menu-img-inner-div">
        <img class="ski-menu-img">
        <img class="ski-menu-img">
      </div>
      </div>
      <div class="ski-menu-stats-div">
        <div class="ski-menu-stat">
          <label>Fart</label>
          <div class="ski-menu-statbar-div">
            <div class="ski-menu-statbar">
              
            </div>
          </div>
        </div>
        <div class="ski-menu-stat">
          <label>Radius</label>
          <div class="ski-menu-statbar-div">
            <div class="ski-menu-statbar">

            </div>
          </div>
        </div>
        <div class="ski-menu-stat">
          <label>Carving</label>
          <div class="ski-menu-statbar-div">
            <div class="ski-menu-statbar">
              
            </div>
          </div>
        </div>
        <div class="ski-menu-stat">
          <label>Freestyle</label>
          <div class="ski-menu-statbar-div">
            <div class="ski-menu-statbar">
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="pause-screen">
  <div class="control-div">
    <h2>Controls</h2>
    <p><b>Sving</b> ← →</p>
    <p><b>Svar</b> ↑</p>
    <p><b>Hop</b> Mellemrum</p>
    <p><b>Spred skiene</b> Z</p>
    <p><b>Kryds skiene</b> Hop + X</p>
    <p><b>360</b> Hop + C</p>
  </div>
  <div class="pause-button-div menu-button-div">
    <button id="fortsæt-button">Fortsæt</button>
    <button id="menu-button">Menu</button>
  </div>
</div>

<div class="overlay-screen">
  <div class="combobar-div animation">
    <div class="carve-time-p">0%</div>
  </div>
  <div class="points-div animation">
    <p style="display: none;">Point: </p>
    <p class="points-p">0</p>
  </div>
</div>

<div class="game-screen">
  <canvas id="canvasBack"></canvas>
  <canvas id="canvasFront"></canvas>
  <div class="field-container-div">
    <div class="field-div left-field-div"></div>
    <div class="border-div"></div>
    <div class="field-div right-field-div"></div>
  </div>
  <div class="game-text">
    <div class="question-div">
      <p class="question-p"></p>
    </div>
    <div class="time-div animation">
      <p class="time-p"></p>
    </div>
    <div class="svar-div svar1-div">
      <p id="svar1-p" class="svar-p"></p>
    </div>
    <div class="svar-div svar2-div">
      <p id="svar2-p" class="svar-p"></p>
    </div>
  </div>
  <div class="ski-div">
    <div class="ski-jump-div animation">
      <div class="ski-rotate-div animation">
        <div class="ski-img-div">
          <img class="ski-img">
          <img class="ski-img">
        </div>
      </div>
    </div>
  </div>
</div>


<?php
  //Scripts
  require "scripts/skis.php";
  require "scripts/questions.php";
  require "scripts/spil.php";
  require "scripts/main.php";
  require "scripts/obstacles.php";
  require "scripts/tricks.php";
  require "scripts/frispil.php";
  require "scripts/menu.php";
  require "scripts/script.php";
?>

</body>
</html>