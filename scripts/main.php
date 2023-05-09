<script>

class Main {
  constructor(){
    this.points = 0;
    this.starterSki = 2;
    this.mode = null;
    this.skis = InitSkiArray(); //array containing all ski objects
    this.ski = null; //current ski
    this.obstacles = new Obstacles();
    this.tricks = new Tricks();
    this.intervals = [];

    //Key event
    this.leftIsDown = false;
    this.rightIsDown = false;

    this.modes = {
      //spil: new Spil(),
      spil: new Spil(),
      frispil: new Frispil(),
      menu: new Menu()
    }


    //Canvas
    this.canvas = {
      canvasBack: () => {
        return document.querySelector("#canvasBack");
      },
      canvas: () => {
        return document.querySelector("#canvasFront");
      },
      ctxBack: () => {
        return document.querySelector("#canvasBack").getContext("2d");
      },
      ctx: () => {
        return document.querySelector("#canvasFront").getContext("2d");
      }
    }

    this.elements = {
      pointsDiv: () => {
        return document.querySelector(".points-div")
      },
      pointsP: () => {
        return document.querySelector(".points-p");
      },
      combobarDiv: () => {
        return document.querySelector(".combobar-div");
      }
    }
  }

  Setup(){

    //Canvas setup
    const canvas = this.canvas.canvas();
    const canvasBack = this.canvas.canvasBack();
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    canvasBack.width = window.innerWidth;
    canvasBack.height = window.innerHeight;

    //Pick starter ski
    this.ChooseSki(this.starterSki);

    //Set ski style
    const skiDiv = document.querySelector(".ski-div");
    skiDiv.style.display = "initial"; 
    skiDiv.style.left = this.ski.x+"px";

    //Find ski y position after skidiv is visible
    this.ski.distFromTop = skiDiv.getBoundingClientRect().top;

    //Add eventlisteners to DOM elements
    this.AddEventListeners();

    //Start the menu
    this.modes.menu.Start();

  }

  Start(){
    this.points = 0;

    //Unfocus all buttons
    document.querySelectorAll("button").forEach(element => {
      element.blur();
    });

    //Clear all current intervals
    this.intervals.forEach((e, i) => {
      clearInterval(this.intervals[i]);
    });

    this.intervals = [];


    //Clears all obstacles from canvas
    this.obstacles.Clear();

    //Loops run function 60 times every second
    let interval = setInterval(() => {
      this.Run();
    }, 1000/60);
    this.intervals.push(interval);
  }

  Run(){
    //Stops game if paused
    if (this.Paused() === true){
      return;
    }
    //
    //    Code below to run on all gamemodes
    //

    //Clears the canvas
    this.ClearCanvas();

    //Update obstacles
    this.obstacles.Update();

    //If skis are jumping
    if (this.tricks.Jumping() === true){
      //Skis to front
      document.querySelector(".ski-div").style.zIndex = "4";
    } else {
      //Skis to back
      document.querySelector(".ski-div").style.zIndex = "1";
      if (this.tricks.cross){
        //Uncross skis when landing
        this.tricks.Uncross();
      }
    }

    //Adds new trail based on new x position
    this.ski.AddNewTrail();

    //Keeps trail length at 30
    this.ski.RemoveOldTrail();

    //Draw trail behind skis
    this.ski.DrawTrail();

    //Rotate skis 
    this.ski.RotateSkis();

    //Move ski x position
    this.ski.MoveSkis();

    //Update x values
    this.ski.UpdateX();

    //Update points
    this.UpdatePoints();
  }

  //Add eventlisteners to DOM elements
  AddEventListeners(){

    //keydown
    document.addEventListener("keydown", this.KeyDown);
    //keyup
    document.addEventListener("keyup", this.KeyUp); 

    //Menu ski hover zoom
    const menuSkiDiv = document.querySelector('.ski-menu-img-div');
    menuSkiDiv.addEventListener("mouseover", () => {
      this.modes.menu.ZoomInSki();
    })
    menuSkiDiv.addEventListener("mouseout", () => {
      this.modes.menu.ZoomOutSki();
    })

    //Menu ski swap buttons
    const swapBtns = document.querySelectorAll(".ski-swap-btn");
    swapBtns.forEach((e, i) => {
      e.addEventListener("click", () => {
        this.modes.menu.SwapSkis(i);
      })
    });

    //
    //  Menu "Træning" and "Frispil" buttons
    //

    //spil
    const spilBtn = document.querySelector("#spil-button");
    spilBtn.addEventListener("click", () => {
      this.modes.spil.Start();
    })

    //frispil
    const frispilBtn = document.querySelector("#frispil-button");
    frispilBtn.addEventListener("click", () => {
      this.modes.frispil.Start();
    })

    //Pause screen buttons
    const fortsaetBtn = document.querySelector("#fortsæt-button");
    fortsaetBtn.addEventListener("click", () => {
      this.Unpause();
    })
    const menuBtn = document.querySelector("#menu-button");
    menuBtn.addEventListener("click", () => {
      this.modes.menu.Start();
    })
  }

  KeyDown(e) {
    if (e.key == "ArrowLeft"){
      main.leftIsDown = true;

      //Swap skis with arrows in menu
      //CURRENTLY DISABLED
      if (main.mode == "Menu"){
        //main.modes.menu.SwapSkis(0);
      }
    }

    if (e.key == "ArrowRight"){
      main.rightIsDown = true;
      //Swap skis with arrows in menu
      //CURRENTLY DISABLED
      if (main.mode === "Menu"){
        //main.modes.menu.SwapSkis(1);
      }
    }

    if (e.key == "Escape"){
      main.TogglePause();
    }

    if (e.key == "Enter"){
      if (main.mode == "Menu"){
        main.modes.spil.Start();
      }
    }

    //Swap skis with 1 2 3 4 keys
    if (e.key == "1" || e.key == "2" || e.key == "3" || e.key == "4"){
      main.ChooseSki(e.key-1);
    }

    if (e.key == " "){
      main.tricks.Jump();
    }

    if (e.key == "x"){
      main.tricks.Cross();
    }

    if (e.key == "z"){
      main.tricks.Spread();
    }

    if (e.key == "c"){
      if (main.leftIsDown){
        main.tricks.Rotate("Left")
      } else {
        main.tricks.Rotate("Right");
      }
    }

    if (e.key == "q" || e.key == "ArrowUp"){
      main.modes.spil.question.ChooseAnswer();
    }
  }

  KeyUp(e) {
    if (e.key == "ArrowLeft"){
      main.leftIsDown = false;
    }

    if (e.key == "ArrowRight"){
      main.rightIsDown = false;
    }

    if (e.key == "x"){
      main.tricks.Uncross();
    }

    if (e.key == "z"){
      main.tricks.Unspread();
    }
  }

  //convert degrees to radians
  DegToRad(deg){
    return deg * (Math.PI/180);
  }

  ShuffleArray(array) {
    let currentIndex = array.length,  randomIndex;

    //While there remains elements to shuffle
    while (currentIndex != 0) {

      //Pick a remaining element
      randomIndex = Math.floor(Math.random() * currentIndex);
      currentIndex--;

      //And swap it with the current element
      [array[currentIndex], array[randomIndex]] = [
        array[randomIndex], array[currentIndex]];
    }

    return array;
  }

  //generate random whole number (int) between 0 and passed parameter
  getRandomInt(max) {
    return Math.floor(Math.random() * max);
  }

  //Checks if element is below canvas (y-axis)
  belowCanvas(e){
    const canvas = this.canvas.canvas();
    if (e.y > canvas.height){
      return true;
    } else {
      return false;
    }
  }

  //Uses CSS animation to set timeout. 
  //Easier this way to stop timeout when pausing 
  //This is done by pausing the animation
  SetAnimationTimeout (time, functionToBeCalled, object) {
    //time is milliseconds
    //functionToBeCalled is the function to call when time has passed
    let div = document.createElement("div");
    div.setAttribute("id", "animation-timeout");
    document.querySelector("body").appendChild(div);

    //CSS animation is used instead of setTimeout().
    //This way, it's easier to stop time when pausing
    //The css @keyframes animation timeout doesn't do anything other than start an animation of nothing with duration = time
    div.classList.add("animation");
    div.style.animation = "timeout " + time + "ms";
    div.addEventListener("animationend", () => {
      //Do this after time has passed
      div.remove();
      
      //call function with object as "this" keyword
      //Used in this case because "this" isn't available or is changed inside the callback function scope
      functionToBeCalled.call(object);
    })


  }


  ShowPoints(){
    const pointsDiv = this.elements.pointsDiv();
    pointsDiv.classList.add("points-show");
  }

  HidePoints(){
    const pointsDiv = this.elements.pointsDiv();
    pointsDiv.classList.remove("points-show");
  }

  UpdatePoints(){
    const pointsP = this.elements.pointsP();
    pointsP.textContent = this.points;
  }

  ShowCombobar(){
    const combobarDiv = this.elements.combobarDiv();
    combobarDiv.classList.add("combobar-show");
  }

  HideCombobar(){
    const combobarDiv = this.elements.combobarDiv();
    combobarDiv.classList.remove("combobar-show");
  }

  HideOverlay(){
    this.HidePoints();
    this.HideCombobar();
  }

  ShowOverlay(){
    this.ShowPoints();
    this.ShowCombobar();
  }

  //Clears canvas
  ClearCanvas(){
    const canvas = this.canvas.canvas();
    const ctx = this.canvas.ctx();
    ctx.clearRect(0, 0, canvas.width, canvas.height);
  }

  //Chooses a ski based on i 
  ChooseSki(i){
    //If switching from another ski, inherit some key values
    if (this.ski){
      this.skis[i].x = this.ski.x;
      this.skis[i].rotation = this.ski.rotation;
      this.skis[i].lastX = this.ski.lastX;
      this.skis[i].distFromTop = this.ski.distFromTop;
      this.skis[i].trail = this.ski.trail;
      this.skis[i].carveTime = 0;
    }
    //Assigns new ski object to global ski variable
    this.ski = this.skis[i];

    //Update ski div size
    const skiDiv = document.querySelector(".ski-div");
    skiDiv.style.width = this.ski.width + "px";

    //Update images
    const skiImgs = document.querySelectorAll(".ski-img");
    skiImgs[0].src = "images/" + this.ski.navn + "Left.png";
    skiImgs[1].src = "images/" + this.ski.navn + "Right.png";

    this.modes.menu.UpdateSkiPanel();

  }

  //Toggles pause screen visibility
  TogglePause(){
    if (this.Paused() === true){
      this.Unpause();
    } else {
      this.Pause();
    }
  }

  //Resumes game from pause state. Continues paused css animations
  Unpause(){

    //Hide pause screen
    let pauseScreen = document.querySelector(".pause-screen");
    pauseScreen.classList.remove("scale1");
    pauseScreen.classList.add("scale0");
    pauseScreen.classList.remove("paused");
    pauseScreen.style.transform = "scale(0)";

    //Stops tricks
    if (this.tricks.cross){
      this.tricks.Uncross();
    }

    if (this.tricks.spread){
      this.tricks.Unspread();
    }

    //Resume css animations
    this.ResumeAllAnimations();

    //Spil-mode only
    if (this.mode == "Spil"){
      this.modes.spil.Resume();
    }
  }

  //Pauses game. Pauses css animations
  Pause(){
    if (this.mode == "Menu"){
      return;
    }

    //Show pause screen
    let pauseScreen = document.querySelector(".pause-screen");
    pauseScreen.classList.remove("scale0");
    pauseScreen.classList.add("scale1");
    pauseScreen.classList.add("paused");
    pauseScreen.style.transform = "scale(1)";
   

    //Pauses animations
    this.PauseAllAnimations();

    //Spil-mode only
    if (this.mode == "Spil"){
      this.modes.spil.Pause();
    }
  }

  Paused(){
    let div = document.querySelector(".paused");
    if (!div){
      return false;
    } else {
      return true;
    }
  }

  ResumeAllAnimations(){
    let div = document.querySelector(".jumping");

    if (div && div.style.animationPlayState == "paused"){
      div.style.animationPlayState = "running";
    }

    if (this.tricks.Rotating()){
      let div = document.querySelector(".ski-rotate-div");
      div.style.animationPlayState = "running";
    }

    //Resume all animations by .animation class
    document.querySelectorAll(".animation").forEach(element => {
      element.style.animationPlayState = "running";
    });
  }

  PauseAllAnimations(){
    if (this.tricks.Jumping()){
      let div = document.querySelector(".jumping");
      div.style.animationPlayState = "paused";
    }

    if (this.tricks.Rotating()){
      let div = document.querySelector(".ski-rotate-div");
      div.style.animationPlayState = "paused";
    }

    //Pause all animations by .animation class
    document.querySelectorAll(".animation").forEach(element => {
      element.style.animationPlayState = "paused";
    });
  }
}

</script>