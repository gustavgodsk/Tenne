<script>

class Menu {
  constructor(){
    this.time = 0;
  }

  Start(){
    main.Start();

    if (main.mode == "Spil"){
      main.modes.spil.Hide();
    } else if (main.mode == "Frispil"){
      main.modes.frispil.Hide();
    }

    if (main.Paused() === true){
      main.Unpause();
    }

    main.mode = "Menu";
    this.time = 0;
    let interval = setInterval(() => {
      this.Run();
    }, 1000/60);
    main.intervals.push(interval);

    this.Show();
  }

  //Runs 60 times a second
  Run(){
    //Stops game if paused
    if (main.isPaused === true){
      return;
    }
    this.time = this.time + 0.02;
  }

  //Updates the right side panel "ski menu" info when changing skis
  UpdateSkiPanel(){
    let skiMenuImgs = document.querySelectorAll(".ski-menu-img");
    skiMenuImgs[0].src = "images/" + main.ski.navn + "Left.png";
    skiMenuImgs[1].src = "images/" + main.ski.navn + "Right.png";

    let skiTitelP = document.querySelector(".ski-navn");
    skiTitelP.textContent = main.ski.navn;

    let skiMenuStatbars = document.querySelectorAll(".ski-menu-statbar");
    //Convert ski.stats object values to array
    let statArray = Object.values(main.ski.stats);

    skiMenuStatbars.forEach((e,i) => {
      e.style.width = (10 - statArray[i]) * 10 + "%";
    });
  }

  //When swapping skis with arrow keys in menu
  SwapSkis(i){
    let currentSkiIndex = main.skis.indexOf(main.ski);
    if (i == 1){
      if (currentSkiIndex < main.skis.length-1){
        main.ChooseSki(currentSkiIndex+1);
      }
    } else if (i == 0){
      if (currentSkiIndex !== 0){
        main.ChooseSki(currentSkiIndex-1);
      }
    }
  }

  //Ski image zoom in/out
  ZoomInSki(){
    document.querySelector('.ski-menu-img-inner-div').style.transform = "rotateX(360deg) rotateY(10deg) scale(1.6)";
  }
  
  ZoomOutSki(){
    document.querySelector('.ski-menu-img-inner-div').style.transform = "rotateX(0) rotateY(0) scale(1)";
  }

  //Scales menu in/out
  Show(){
    let menuScreen = document.querySelector(".menu-screen");
    menuScreen.classList.remove("scale0");
    menuScreen.classList.add("scale1");
    menuScreen.style.transform = "scale(1)";
  }

  Hide(){
    let menuScreen = document.querySelector(".menu-screen");
    menuScreen.classList.remove("scale1");
    menuScreen.classList.add("scale0");
    menuScreen.style.transform = "scale(0)";
  }
}












</script>