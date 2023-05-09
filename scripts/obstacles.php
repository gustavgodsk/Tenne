<script>

class Obstacles {
  constructor(){
    this.treeController = new TreeController();
    this.gateController = new GateController();
  }

  Clear(){
    this.treeController.Clear();
    this.gateController.Clear();
  }

  Update(){
    this.RemoveOffScreenObstacles();
    this.treeController.MoveTrees();
    this.gateController.MoveGates();
    this.CheckCollisionAll(this.treeController.trees);
    this.CheckCollisionAll(this.gateController.gates);

  }

  CheckCollisionAll(arrayOfObstacles){
    arrayOfObstacles.forEach((el, i) => {
      this.IsColliding(el);
    })
  }

  IsColliding(div){
    const skiImgDiv = document.querySelector(".ski-img-div");
    const skiRect = skiImgDiv.getBoundingClientRect();

    //Center position of ski, x-coordinate
    const xCenter = skiRect.x + (skiRect.right - skiRect.left) / 2;

    //Center position of ski, y-coordinate
    const yCenter = window.innerHeight - 220


    //HITBOX
    let top = yCenter - 20;
    let bot = yCenter + 20;
    let left = xCenter - 10;
    let right = xCenter + 10;

    //if obstacle y position is within hitbox
    if (div.y + 20 < bot && div.y + 80 > top && !main.tricks.Jumping()){

      
      //if obstacle x position is within hitbox
      //... if obstacle is hit
      if (div.left + 20 < right && div.left + 80 > left){
        console.log("HIT")
        const ctx = main.canvas.ctx();
        ctx.beginPath();
        ctx.fillStyle = "red";
        ctx.arc(div.left+50, div.y+50, 50, 0, 2 * Math.PI);
        ctx.fill();

        //Decrease carveTime when hit
        main.ski.carveTime -= 20;

        //End game if hitting obstacle when at 0 carveTime
        if (main.ski.carveTime <= 0 && main.mode == "Spil"){
          main.modes.spil.Die("Obstacle");
        }
      }

    } else {
      //if obstacle is NOT hit
    }
  }

  RemoveOffScreenObstacles(){
    this.treeController.RemoveOffScreen();
    this.gateController.RemoveOffScreen();
  }

  SpawnTrees(){
    //Start spawning trees
    let interval = setInterval(() => {
      if (Math.random() < 0.5){ //50% chance to spawn tree
        this.treeController.SpawnNewTree();
      }
    }, 1000/3); //interval between each spawn
    main.intervals.push(interval);
  }

  SpawnGates(){
    //Start spawning gates
    let interval = setInterval(() => {
      if (Math.random() < 0.5){ //50% chance to spawn gate
        this.gateController.SpawnNewGate();
      }
    }, 1000/2);
    main.intervals.push(interval);
  }
}

//Class to control all trees
class TreeController {
  constructor(){
    this.trees = new Array();
  }

  //Add new tree to array and draw it on canvas
  SpawnNewTree(){
    //prevents spawning a forest when pausing
    if (main.Paused() == true){
      return;
    }
    let x = 70 * Math.random() + 15;
    let newTree = new Tree(x);
    this.trees.push(newTree);
    newTree.Draw();

    //chance to spawn another tree
    if (Math.random() < 0.15) {
      this.SpawnNewTree();
    }
  }

  //Removes trees from DOM when they leave the canvas
  RemoveOffScreen(){
    this.trees.forEach((e, i) => {
      if (main.belowCanvas(e)){
        this.trees.splice(i, 1);
        e.img.remove();
      }
    });
  }

  //Removes all trees from canvas
  Clear(){
    this.trees.forEach((e, i) => {
      e.img.remove();
    });
    this.trees = [];
  }

  MoveTrees(){
    this.trees.forEach((e, i) => {
      e.Move();
    });
  }
}

//Tree class
class Tree {
  constructor(x){
    this.x = x;
    this.y = -150;
    this.yFart = 8;
    this.left = (x/100) * window.innerWidth;
    this.img = document.createElement("img")
    this.img.src = "images/tree"+ main.getRandomInt(3) +".png"
    this.img.classList.add("tree");
  }

  Move(){
    //tree y position
    this.y += main.ski.yFart - Math.abs(main.ski.rotation) * main.ski.yAcceleration;
    this.img.style.top = this.y + "px";

    //x position in percentage
    this.x -= main.ski.xFart/20; 
    this.img.style.left = this.x + "%";

    //update x position in pixels
    this.left = (this.x/100) * window.innerWidth;
  }

  Draw(){
    this.img.style.top = this.y + "px";
    document.querySelector(".game-screen").appendChild(this.img)
  }
}


//Class to control all trees
class GateController {
  constructor(){
    this.gates = new Array();
  }

  //Add new tree to array and draw it on canvas
  SpawnNewGate(){
    let x = 50;
    let newGate = new Gate(x);
    this.gates.push(newGate);
    newGate.Draw();
  }

  //Removes trees from DOM when they leave the canvas
  RemoveOffScreen(){
    this.gates.forEach((e, i) => {
      if (main.belowCanvas(e)){
        this.gates.splice(i, 1);
        e.img.remove();
      }
    });
  }

  //Removes all trees from canvas
  Clear(){
    this.gates.forEach((e, i) => {
      e.img.remove();
    });
    this.gates = [];
  }

  MoveGates(){
    this.gates.forEach((e, i) => {
      e.Move();
    });
  }
}

//Tree class
class Gate {
  constructor(x){
    this.x = x;
    this.y = -150;
    this.yFart = 8;
    this.img = document.createElement("img");
    this.img.src = "images/gate.png";
    this.img.classList.add("gate");
  }

  Move(){
    //y position
    this.y += main.ski.yFart - Math.abs(main.ski.rotation) * main.ski.yAcceleration;
    this.img.style.top = this.y + "px";

    //x position
    this.x -= main.ski.xFart/20; 
    this.img.style.left = this.x + "%";

  }

  Draw(){
    this.img.style.top = this.y + "px";
    document.querySelector(".game-screen").appendChild(this.img)
  }
}

  
</script>