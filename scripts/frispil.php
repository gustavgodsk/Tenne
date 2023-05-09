<script>

class Frispil {
  constructor(){

  }

  Start(){
    main.mode = "Frispil";
    main.Start();
    main.modes.menu.Hide();
    main.obstacles.SpawnTrees();
    //main.obstacles.SpawnGates();

    this.Show();

    let interval = setInterval(() => {
      this.Run();
    }, 1000/60);
    main.intervals.push(interval);
  }

  Run(){
    //Stops game if paused
    if (main.Paused() === true){
      return;
    }
  }

  Show() {
    main.ShowOverlay();
  }

  Hide() {
    main.HideOverlay();
  }
}




</script>