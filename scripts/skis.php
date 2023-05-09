<script>

//Initialize skis
function InitSkiArray(){
  //Array to return
  let skiArray = [];

  //Create 4 ski objects of different types
  skiArray.push(new Ski(new AllMountain()));
  skiArray.push(new Ski(new SL()));
  skiArray.push(new Ski(new GS()));
  skiArray.push(new Ski(new TwinTip()));
  
  //Return array with ski objects
  return skiArray;
}



//Ski class to perform actions that are similar on all skis
class Ski {
  constructor(type){
    //Innate values (same on all skis)
    this.x = 0;
    this.rotation = 0;
    this.xFart = 0;
    this.carveTime = 0;
    this.trail = [];
    this.lastX = 0;
    this.distFromTop = 0;

    //Type-specific values
    //Passed as argument from type object
    this.type = type;
    this.navn = type.navn;
    this.rotationDirection = type.rotationDirection;
    this.rotationSpeed = type.rotationSpeed;
    this.xAcceleration = type.xAcceleration;
    this.maxCarveTime = type.maxCarveTime;
    this.yFart = type.yFart;
    this.yAcceleration = type.yAcceleration;
    this.pivotSpeed = type.pivotSpeed;
    this.carveDecay = type.carveDecay;
    this.carveRamp = type.carveRamp;
    this.stats = type.stats;
    this.distFromBot = type.distFromBot;
    this.trailParam1 = type.trailParam1;
    this.trailParam2 = type.trailParam2;
    this.width = type.width;
  }

  AddNewTrail(){
    //Stops trail when jumping
    if (main.tricks.Jumping()){
      return;
    }
    //Add trail to beginning of array
    this.trail.unshift(
      [
        this.x,
        this.lastX
      ]
    );
  }

  RemoveOldTrail(){
    //Removes trail out of screen and when jumping
    if (this.trail.length > 30 || main.tricks.Jumping()) {
      this.trail.pop();
    }
  }

  DrawTrail(){
    const canvas = main.canvas.canvas();
    const ctx = main.canvas.ctx();
    this.trail.forEach((e, i) => {
      //fade end of trail with transparency
      ctx.strokeStyle = "rgba(0,0,0," + 0.002 * (2 * (this.trail.length-i)) + ")"; 
      ctx.lineWidth = 5;

      //Trail x and y positions to draw lines between
      let x1 = e[1]+this.trailParam1 - Math.sin(main.DegToRad(this.rotation))*60;
      let y1 = (this.trailParam1 + this.rotation * 0.05 + Math.cos(main.DegToRad(this.rotation)) * 60 + this.distFromTop + this.distFromBot - (this.distFromBot / 30) * (this.trail.length-i-1));

      let x2 = e[0]+this.trailParam1 - Math.sin(main.DegToRad(this.rotation))*60;
      let y2 = (this.trailParam1 + this.rotation * 0.05 + Math.cos(main.DegToRad(this.rotation)) * 60 + this.distFromTop + this.distFromBot - (this.distFromBot / 30) * (this.trail.length-i));

      let x3 = e[1]+this.trailParam2 - Math.sin(main.DegToRad(this.rotation))*60;
      let y3 = (this.trailParam1 + this.rotation * 0.05 + Math.cos(main.DegToRad(this.rotation)) * 60 + this.distFromTop + this.distFromBot - (this.distFromBot / 30) * (this.trail.length-i-1));

      let x4 = e[0]+this.trailParam2 - Math.sin(main.DegToRad(this.rotation))*60;
      let y4 = (this.trailParam1 + this.rotation * 0.05 + Math.cos(main.DegToRad(this.rotation)) * 60 + this.distFromTop + this.distFromBot - (this.distFromBot / 30) * (this.trail.length-i));

      //Left trail
      ctx.beginPath();
      ctx.moveTo(x1, y1);
      ctx.lineTo(x2, y2);
      ctx.stroke();

      //Right trail
      ctx.beginPath();
      ctx.moveTo(x3, y3);
      ctx.lineTo(x4, y4);
      ctx.stroke();
    });
  }

  RotateSkis(){
    if (main.mode !== "Menu"){
      //Rotation direction by arrow keys
      //Ramps up carveTime when turning
      if (main.leftIsDown){
        this.rotationDirection = -1;
        if (!main.tricks.Jumping()){
          this.carveTime += this.carveRamp;
        }
      }

      if (main.rightIsDown){
        this.rotationDirection = 1;
        if (!main.tricks.Jumping()){
          this.carveTime += this.carveRamp;
        }
      }

      if ((!main.rightIsDown && !main.leftIsDown) || (main.rightIsDown && main.leftIsDown)){
        this.rotationDirection = 0;
        //reduce carveTime when skis stop turning
        this.carveTime -= this.carveDecay;
      }

      //carveTime min value to 0
      if (this.carveTime < 0){
        this.carveTime = 0;
      }

      //carveTime max value to 600
      if (this.carveTime > this.maxCarveTime){
        this.carveTime = this.maxCarveTime;
      }
      document.querySelector(".carve-time-p").textContent = Math.round(this.carveTime/6) + "%";

      //Update css root variable
      const root = document.querySelector(':root');
      //root.style.setProperty('--combobar', this.carveTime/6 + "%");

      const carveTimeP = document.querySelector(".carve-time-p");
      carveTimeP.style.width = this.carveTime/6 + "%";

      if (this.carveTime < this.maxCarveTime * 0.3){
        carveTimeP.style.backgroundColor = "red";
      } else {
        carveTimeP.style.backgroundColor = "rgb(116, 194, 92)";
        
      }

      //Sets new rotation value
      this.rotation += (this.rotationSpeed + ((this.carveTime/2) * this.pivotSpeed) / 100) * this.rotationDirection;
    } 
    //Automates rotation in menu
    else if (main.mode == "Menu"){
      this.rotation = Math.sin(main.modes.menu.time) * 30;
    }

    //Prevents skis from rotating more than 90 degrees in both directions
    if (this.rotation > 90){
      this.rotation = 90;
      this.carveTime -= this.carveDecay/2;
    }

    if (this.rotation < -90){
      this.rotation = -90;
      this.carveTime -= this.carveDecay/2;
    }
    


    //Rotate skis based on this.rotation value
    const skiImgDiv = document.querySelector(".ski-img-div");
    skiImgDiv.style.transform = "rotateZ("+ this.rotation +"deg)";
  }

  MoveSkis(){
    const canvas = main.canvas.canvas();
    //Prevent skis from leaving screen horizontally
    //Reduces carveTime when hitting wall
    if (this.x+88 < 0){
      this.x = -88;
      this.carveTime -= this.carveDecay/4;
    }
    if (this.x+113 > canvas.width){
      this.x = canvas.width-113;
      this.carveTime -= this.carveDecay/4;
    }
    
    //Set ski x position
    const skiDiv = document.querySelector(".ski-div");
    skiDiv.style.left = this.x+"px";
  }


  UpdateX() {
    //Ski moves faster horizontally when rotated  
    this.xFart = (this.xAcceleration + this.carveTime / 100) * Math.sin(main.DegToRad(this.rotation))

    //Updates lastX and this.x
    this.lastX = this.x;
    this.x += this.xFart;

    //Update CSS root variable "skiX" to new ski x pos
    document.querySelector(':root').style.setProperty('--skiX', this.x + "px");
  }

  //Add points
  AddPoints(points){
    const overlay = document.querySelector(".overlay-screen");

    //Update CSS root variable "pointX" to new ski x 
    let pointX = this.x + 50 ;
    if (this.x < 0) {
      pointX = 20;
    }
    document.querySelector(':root').style.setProperty('--pointX', pointX + "px");

    const el = document.createElement("p");
    el.textContent = points;
    el.classList.add("point-p");
    el.classList.add("animation");
    el.addEventListener("animationend", function placeholderName(){
      el.remove();
      main.points += points;
    })

    overlay.appendChild(el);

  }

}

/*

Individual classes for each type of ski to perform type-specific actions

Contains type-specific variables

*/
class AllMountain {
  constructor(){
    this.navn = "All Mountain";
    this.rotationDirection = 1;
    this.rotationSpeed = 0.6;
    this.xAcceleration = 20;
    this.maxCarveTime = 600;
    this.yFart = 12;
    this.yAcceleration = 0.05;
    this.pivotSpeed = 1;
    this.carveDecay = 20;
    this.carveRamp = 1;
    this.distFromBot = 200;
    this.trailParam1 = 73;
    this.trailParam2 = 97
    this.width = 200;

    this.stats = {
      fart: 5,
      radius: 5,
      carving: 5,
      freestyle: 5
    }
  }
}

class SL {
  constructor(){
    this.navn = "SL";
    this.rotationDirection = 1;
    this.rotationSpeed = 0.6;
    this.xAcceleration = 20;
    this.maxCarveTime = 600;
    this.yFart = 12;
    this.yAcceleration = 0.05;
    this.pivotSpeed = 1;
    this.carveDecay = 20;
    this.carveRamp = 1;
    this.distFromBot = 200;
    this.trailParam1 = 73;
    this.trailParam2 = 97
    this.width = 200;

    this.stats = {
      fart: 7,
      radius: 2,
      carving: 8,
      freestyle: 4
    }
  }
}

//Individual class to contain type-specific values
class GS {
  constructor(){
    //GS-specific values
    this.navn = "GS";
    this.rotationDirection = 1;
    this.rotationSpeed = 0.3;
    this.xAcceleration = 10;
    this.maxCarveTime = 600;
    this.yFart = 8;
    this.yAcceleration = 0.05;
    this.pivotSpeed = 1;
    this.carveDecay = 10;
    this.carveRamp = 2;
    this.distFromBot = 200;
    this.trailParam1 = 92;
    this.trailParam2 = 108;
    this.width = 200;

    //Menu stats
    this.stats = {
      fart: 10,
      radius: 9,
      carving: 9,
      freestyle: 1
    }
  }
}

class TwinTip {
  constructor(){
    this.navn = "Twin Tip";
    this.rotationDirection = 1;
    this.rotationSpeed = 0.6;
    this.xAcceleration = 20;
    this.maxCarveTime = 600;
    this.yFart = 12;
    this.yAcceleration = 0.05;
    this.pivotSpeed = 1;
    this.carveDecay = 20;
    this.carveRamp = 1;
    this.distFromBot = 200;
    this.trailParam1 = 73;
    this.trailParam2 = 97
    this.width = 200;

    this.stats = {
      fart: 5,
      radius: 5,
      carving: 1,
      freestyle: 10
    }
  }
}

</script>