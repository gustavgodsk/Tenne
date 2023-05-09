<script>



class Training {
  constructor(){
    this.question = null;
    this.background = null;
    this.svarmuligheder = null;

    this.immunityTimeLimit = 60; //death after 1 second of 0 carveTime
    this.immunityTime = 0; //time elapsed, +1 each Run() iteration
    this.invincible = true; //specifices whether able to die
  }

  Start(){
    main.mode = "Training";
    main.Start();
    main.modes.menu.Hide();
    this.question = new Question();
    this.background = new Background();
    this.svarmuligheder = new Svarmuligheder();
    this.background.Show();
    main.obstacles.SpawnTrees();
    this.invincible = true; //unable to die at beginning of game
    main.ShowOverlay();


    //Call this.Run() function 60 times/sec
    let interval = setInterval(() => {
      this.Run();
    }, 1000/60);
    main.intervals.push(interval);

    //Call this.Run() function 60 times/sec
    interval = setInterval(() => {
      this.DecreaseTime();
    }, 1000/10);
    main.intervals.push(interval);
    
    //Show question after 2 sec
    setTimeout(() => {
      this.question.Next();
    }, 2000);
  }

  //60 runs / sec
  Run(){
    //Stops game if paused
    if (main.Paused() === true){
      return;
    }
    this.background.Move();
    this.background.Select();

    //When 0 carveTime
    if (main.ski.carveTime <= 0 && this.invincible === false){
      this.immunityTime += 1;
      //If immunity time surpasses the limit, end game
      if (this.immunityTime >= this.immunityTimeLimit){
        this.Die("immunityTimeLimit");
      }
    } 
    //Reset immunity time when carveTime > 0
    else {
      this.immunityTime = 0;
    }

    console.log(this.immunityTime)
  }

  DecreaseTime(){
    //Stops if paused
    if (main.Paused() === true){
      return;
    }
    if (this.question.isReady === true){
      //if time is up
      if (this.question.time < 0.1){
        this.question.time = 0;
        this.question.ChooseAnswer();
      } else {
        this.question.time -= 0.1;
      }
    }
    document.querySelector(".time-div").textContent = this.question.time.toFixed(0);
  }

  Die(cause){
    //Dont die when invincible
    if (this.invincible === true){
      return;
    }

    this.question.StopNextQuestion();

    console.log("Died from: " + cause);
    //resets immunityTime
    this.immunityTime = 0;
    this.EndScreen();
  }

  //Show end screen details, statistics, ...
  EndScreen(){
    this.Quit();
  }

  //Hide everything and return to Menu
  Quit(){
    this.Hide();
    main.modes.menu.Start();
  }

  //Hide training mode
  Hide(){
    this.question.Hide();
    this.background.Hide();
    this.svarmuligheder.SlideOut();
    main.HideOverlay();
    this.question.StopNextQuestion();
  }

  Pause(){
    this.question.PauseAnimations();
    this.background.PauseAnimations();
    this.svarmuligheder.PauseAnimations();

    //hide text to avoid cheating
    this.question.elements.Div().style.visibility = "hidden";
    this.svarmuligheder.elements.svarDivs[0].style.visibility = "hidden";
    this.svarmuligheder.elements.svarDivs[1].style.visibility = "hidden";

  }

  Resume(){
    this.question.ResumeAnimations();
    this.background.ResumeAnimations();
    this.svarmuligheder.ResumeAnimations();
    
    //make text visible again
    this.question.elements.Div().style.visibility = "visible";
    this.svarmuligheder.elements.svarDivs[0].style.visibility = "visible";
    this.svarmuligheder.elements.svarDivs[1].style.visibility = "visible";

  }
}

class Question {
  constructor(){
    this.i = -1; //index in question array
    this.questions = main.ShuffleArray(FetchQuestions()); //randomizes order of questions
    this.currentQuestion = null;
    this.isReady = false; //specifies the point at which the player should be allowed to cast answer
    this.startTime = 6; //answer countdown
    this.time = this.startTime;

    this.elements = {
      Div: () => {
        return document.querySelector(".question-div");
      },
      p: document.querySelector(".question-p"),
      timeDiv: document.querySelector(".time-div"),
      timeP: document.querySelector(".time-p")
    }
  }

  //Next question
  Next(){
    //If not questions left in array
    if (this.i + 1 >= this.questions.length){
      main.modes.training.EndScreen();
      return;
    }
    //Choose next question in array
    this.i += 1;
    this.currentQuestion = this.questions[this.i];

    //Update question text
    this.elements.p.textContent = this.currentQuestion.spørgsmål;

    //Show question
    this.Show();

    //Update answers
    this.UpdateSvarmuligheder();
  }

  //Show question
  Show(){
    let el = this.elements.Div();

    el.classList.add("question-show");

    el.addEventListener("animationend", this.Ready)
  }


  //Begin the game
  Ready(){

    main.modes.training.question.elements.Div().removeEventListener("animationend", this.Ready);

    //Svarmuligheder slide in
    main.modes.training.svarmuligheder.SlideIn();

    main.ShowOverlay();

    //Makes player able to die
    main.modes.training.invincible = false;

  }

  UpdateSvarmuligheder(){
    let Ps = document.querySelectorAll(".svar-p");

    //Randomizes the side of correct answer
    if (Math.random() < 0.5){
      Ps[0].textContent = this.currentQuestion.rigtigtSvar[0];
      Ps[1].textContent = this.currentQuestion.forkertSvar[0];
    } else {
      Ps[1].textContent = this.currentQuestion.rigtigtSvar[0];
      Ps[0].textContent = this.currentQuestion.forkertSvar[0];
    }



  }

  ChooseAnswer(){
    if (!this.isReady || main.Paused()){
      return;
    }
    this.isReady = false;
    main.modes.training.background.canSelect = false;


    //If wrong answer
    if (main.modes.training.svarmuligheder.GetSelectedAnswer().textContent !== this.currentQuestion.rigtigtSvar[0]){
      this.WrongAnswer();
    } 
    //If right answer
    else {
      this.RightAnswer();
    }

    //3000 ms timeout
    //call PostQuestionScreen() after time
    //pass "this" at parameter to allow PostQuestionScreen() to access "this" keyword
    main.SetAnimationTimeout(4000, this.PostQuestionScreen, this);
  }

  WrongAnswer(){
    main.modes.training.background.GetSelectedField().style.backgroundColor = "#ff4d4d";
  }

  RightAnswer(){
    main.modes.training.background.GetSelectedField().style.backgroundColor = "#2bff4b";

    //add points if correct answer
    const points = Math.floor(500 + 0.5 * main.ski.carveTime + 50 * (1 + this.time));

    main.ski.AddPoints(points);
  }

  PostQuestionScreen(){

    //if multiple answers and at last question, make conclusion screen 
    if (this.currentQuestion.rigtigtSvar.length > 1 && this.i - 1 == this.currentQuestion.rigtigtSvar.length){
      this.ConclusionScreen();
    } else {
      this.ResetQuestion();
    }
  }

  ResetQuestion(){
    main.modes.training.background.ToCenter();
    main.modes.training.svarmuligheder.SlideOut();
    this.Hide();

    //Next question after 1 sec
    main.SetAnimationTimeout(1000, this.Next, this);
  }

  Hide(){
    this.elements.Div().classList.remove("question-show")
  }

  StopNextQuestion(){
    //Stops timeout for next question
    if (document.querySelector("#animation-timeout")) {
      document.querySelector("#animation-timeout").remove();
    }
  }

  PauseAnimations(){
    this.elements.Div().style.animationPlayState = "paused";
  }

  ResumeAnimations(){
    this.elements.Div().style.animationPlayState = "running";
  }

  GetSelectedIndex(){
    let answerI;
    main.modes.training.svarmuligheder.elements.svarDivs.forEach((e, i) => {
      if (e.classList.contains("follow-ski-right") || e.classList.contains("follow-ski-left")){
        answerI = i;
      }
    });
    return answerI;
  }
}


//Background class
class Background {
  constructor(){
    this.canMove = false;
    this.canSelect = false;
    this.visible = false;
    //grid-template-columns css property
    //determines horizontal placement of border
    this.fieldWidth = [49.5, 1, 49.5];

    this.elements = {
      container: document.querySelector(".field-container-div"),
      fields: document.querySelectorAll(".field-div")
    }
  }


  Show(){
    let el = this.elements.container;

    el.classList.add("background-show");
    el.style.left = "0";
    this.visible = true;

    //When animation is done, remove class
    el.addEventListener("animationend", () => {
      el.classList.remove("background-show");
      this.canMove = true;
      this.canSelect = true;
    });
  }

  Hide(){
    if (!this.visible){
      return;
    }
    let el = this.elements.container;

    el.classList.remove("background-show");
    el.classList.add("background-hide");
    el.style.left = "-100%";

    this.elements.fields[0].style.backgroundColor = "transparent";
    this.elements.fields[1].style.backgroundColor = "transparent";
    this.visible = false;

    //When animation is done, remove class
    el.addEventListener("animationend", () => {
      el.classList.remove("background-hide");
      this.canMove = false;
      this.canSelect = false;
    });
  }

  Select(){

    if (!this.canSelect){
      return;
    }

    let fields = this.elements.fields;


    if (fields[0].clientWidth > main.ski.x + document.querySelector(".ski-img-div").clientWidth/2 - document.querySelector(".border-div").clientWidth){

      //Select left field
      fields[0].style.backgroundColor = "#7dc2e3";
      fields[1].style.backgroundColor = "transparent";

      let svarmuligheder = main.modes.training.svarmuligheder;
      if (svarmuligheder.selectable){
        svarmuligheder.SelectAnswer(0);
        svarmuligheder.UnselectAnswer(1);
      }


    } else {
      //Select right field
      fields[1].style.backgroundColor = "#7dc2e3";
      fields[0].style.backgroundColor = "transparent";

      let svarmuligheder = main.modes.training.svarmuligheder;
      if (svarmuligheder.selectable){
        svarmuligheder.SelectAnswer(1);
        svarmuligheder.UnselectAnswer(0);
      }

    }
  }

  Move(){
    if (!this.canMove){
      return;
    }

    //Move background horizontally
    this.elements.container.style.gridTemplateColumns = this.fieldWidth[0] + "% " + this.fieldWidth[1] + "% " + this.fieldWidth[2] + "% ";

    //Update css root variables
    const root = document.querySelector(':root');
    root.style.setProperty('--fieldWidth1', this.fieldWidth[0] + "%");
    root.style.setProperty('--fieldWidth2', this.fieldWidth[1] + "%");
    root.style.setProperty('--fieldWidth3', this.fieldWidth[2] + "%");


    //Update for next cycle
    this.fieldWidth[0] -= main.ski.xFart/20;
    this.fieldWidth[2] += main.ski.xFart/20;

    if (this.fieldWidth[0] > 99){
      this.fieldWidth[0] = 99;
    }
    if (this.fieldWidth[2] > 99){
      this.fieldWidth[2] = 99;
    }
    if (this.fieldWidth[0] < 0){
      this.fieldWidth[0] = 0;
    }
    if (this.fieldWidth[2] < 0){
      this.fieldWidth[2] = 0;
    }


  }

  //Moves background to center position
  ToCenter(){
    let el = this.elements.container;
    this.canMove = false;
    this.canSelect = false;
    el.classList.add("to-center");
    this.elements.fields.forEach(element => {
      element.style.backgroundColor = "transparent";
    }); 
    this.fieldWidth = [49.5, 1, 49.5]

    el.addEventListener("animationend", function placeholderName(){
      main.modes.training.background.UnCenter();
    })

  }

  //Makes background move according to ski position
  UnCenter(){
    let el = this.elements.container;
    el.removeEventListener("animationend", this.placeholderName);

    el.classList.remove("to-center");
    el.style.gridTemplateColumns = "49.5% 1% 49.5%"; 
    main.modes.training.background.canSelect = true;
    main.modes.training.background.canMove = true;

  }

  GetSelectedField(){
    let selectedField;
    this.elements.fields.forEach((e, i) => {
      if (e.style.backgroundColor !== "transparent"){
        selectedField = e;
      }
    });
    return selectedField;
  }

  PauseAnimations(){
    this.elements.container.style.animationPlayState = "paused";
    this.elements.fields.forEach(element => {
      element.style.animationPlayState = "paused";
    });
  }
  
  ResumeAnimations(){
    this.elements.container.style.animationPlayState = "running";
    this.elements.fields.forEach(element => {
      element.style.animationPlayState = "running";
    });
  }
}

//Svarmuligheder class
class Svarmuligheder {
  constructor(){
    this.selectable = false;

    this.elements = {
      svarPs: document.querySelectorAll(".svar-p"),
      svarDivs: document.querySelectorAll(".svar-div")
    }
  }

  SelectAnswer(i){
    if (!this.selectable){
      return;
    }
    const canvas = main.canvas.canvas();

    let el = this.elements.svarDivs[i];

    let skiLeftDistance = main.ski.x - el.clientWidth/4;
    let skiRightDistance = canvas.width - main.ski.x - 180;

    if (skiLeftDistance < 0){
      skiLeftDistance = 0;
    }

    if (skiRightDistance < 0){
      skiRightDistance = 0;
    }

    //Update CSS root variables
    let root = document.querySelector(':root');

    root.style.setProperty('--skiLeftDistance', skiLeftDistance + "px");
    root.style.setProperty('--skiRightDistance', skiRightDistance + "px");

    

    //If left answer chosen
    if (i == 0){

      el.classList.remove("unfollow-ski-left");
      //follow new answer
      el.classList.add("follow-ski-left");    
    } 
    //If right answer chosen
    else if (i == 1){
      //follow new answer
      el.classList.add("follow-ski-right");
      el.classList.remove("unfollow-ski-right");      
    }

  }

  UnselectAnswer(i){
    if (!this.selectable){
      return;
    }
    
    let el = this.elements.svarDivs[i];
    

    //If left answer chosen
    if (i == 0){
      if (el.classList.contains("follow-ski-left")){
        el.classList.add("unfollow-ski-left");
      }
    el.classList.remove("follow-ski-left");
    } 
    //If right answer chosen
    else if (i == 1){
      if (el.classList.contains("follow-ski-right")){
        el.classList.add("unfollow-ski-right");
      }
      el.classList.remove("follow-ski-right");
    }
  }

  Otheri(i){
    if (i == 0){
      return 1;
    } else if (i == 1){
      return 0;
    }
  }

  GetSelectedAnswer(){
    let answerP;
    this.elements.svarDivs.forEach((e, i) => {
      if (e.classList.contains("follow-ski-right") || e.classList.contains("follow-ski-left")){
        answerP = this.elements.svarPs[i];
      }
    });
    return answerP;
  }

  
  SlideOut(){
    this.selectable = false;
    let el = this.elements.svarDivs;
    
    el.forEach((e, i) => {
      e.className = "svar-div svar" + (i + 1) + "-div";
      e.classList.add("slide-out");

    });

    //Slide in time div
    const timeDiv = main.modes.training.question.elements.timeDiv;
    const timeP = main.modes.training.question.elements.timeP;

    //Slide out time div
    timeDiv.classList.remove("time-slide-in");
    timeDiv.classList.add("time-slide-out");
    
  }

  SlideIn(){
    //Reset time
    main.modes.training.question.time = main.modes.training.question.startTime;
    
    //Slide in svarmuligheder
    let el = this.elements.svarDivs;
    el.forEach((e, i) => {
      e.classList.remove("slide-out")
      e.classList.add("slide-in");
      e.addEventListener("animationend", function placeholderName () {
        //Prevent future duplicates
        e.removeEventListener("animationend", placeholderName);
        
        //When ready to answer, "isReady"
        main.modes.training.question.isReady = true;
        main.modes.training.svarmuligheder.selectable = true;
      });
    });

    //Slide in time div
    const timeDiv = main.modes.training.question.elements.timeDiv;
    const timeP = main.modes.training.question.elements.timeP;

    timeDiv.classList.remove("time-slide-out");
    timeDiv.classList.add("time-slide-in");

  }

  PauseAnimations(){
    this.elements.svarDivs.forEach(element => {
      element.style.animationPlayState = "paused";
    });
  }

  ResumeAnimations(){
    this.elements.svarDivs.forEach(element => {
      element.style.animationPlayState = "running";
    });
  }
}

</script>