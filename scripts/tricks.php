<script>

class Tricks {
  constructor(){
    this.cross = false;
    this.spread = false;
    this.switch = false;
  }

  //Checks if skis are jumping and returns bool
  Jumping(){
    //Searches for element with .jumping class
    const div = document.querySelector(".jumping");
    //If no element found, ski is not jumping
    if (!div){
      return false;
    } 
    //If element is found and animation is not paused, ski is jumping
    else if (div.style.animationPlayState !== "paused"){
      return true;
    }
  }

  //Make skis perform a jump
  Jump() {
    //Unable to jump when below conditions are met 
    if (main.mode == "Menu" || this.Jumping() === true || main.Paused() === true){
      return;
    }

    //HTML ski element
    const div = document.querySelector(".ski-jump-div"); 
    //Add .jumping class to element
    div.classList.add("jumping");

    //When jump animation is done, remove jumping class
    div.addEventListener("animationend", () => {
      div.classList.remove("jumping");
    });
  }
  
  //Cross skis
  Cross(){
    if (main.Paused() || !this.Jumping() || main.mode == "Menu"){
      return;
    }

    let imgs = document.querySelectorAll(".ski-img")

    imgs[0].classList.add("crossSkiLeft");
    imgs[1].classList.add("crossSkiRight");

    this.cross = true;
  }

  //Uncross skis
  Uncross() {
    if (main.Paused()){
      return;
    }
    let imgs = document.querySelectorAll(".ski-img")

    imgs[0].classList.remove("crossSkiLeft");
    imgs[1].classList.remove("crossSkiRight");

    this.cross = false;
  }

  //Spread skis
  Spread() {
    if (main.Paused()){
      return;
    }
    let imgs = document.querySelectorAll(".ski-img")

    imgs[0].classList.add("spreadLeft");
    imgs[1].classList.add("spreadRight");

    this.spread = true;
  }

  //Unspread skis
  Unspread() {
    if (main.Paused()){
      return;
    }
    let imgs = document.querySelectorAll(".ski-img")

    imgs[0].classList.remove("spreadLeft");
    imgs[1].classList.remove("spreadRight");

    this.spread = false;
  }

  //Rotate skis in "dir" direction
  Rotate(dir) {

    if (main.mode == "Menu" || !this.Jumping() || main.Paused() || this.Rotating()){
      return;
    }
    let div = document.querySelector(".ski-rotate-div");
    
    div.classList.add("rotating" + dir);
    div.classList.add("rotating");

    //When rotation animation is done, remove rotating class
    div.addEventListener("animationend", () => {
      div.classList.remove("rotating" + dir);
      div.classList.remove("rotating");
    });
  }

  //Returns bool whether ski is currently performing a rotate animation
  Rotating() {
    let div = document.querySelector(".rotating");
    if (!div){
      return false;
    } 
    else if (div.style.animationPlayState !== "paused"){
      return true;
    }
  }
}


</script>