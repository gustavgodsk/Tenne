<script>

//Global object
let main;
  
function Setup(){

  main = new Main();
  main.Setup();

}


//
//   Wait until document is loaded before executing javascript Setup() function 
//

// in case the document is already rendered
if (document.readyState !== 'loading'){
  Setup();
}
// modern browsers
else if (document.addEventListener){
  document.addEventListener('DOMContentLoaded', Setup());
} 
// Older than IE 8
else {
  document.attachEvent('onreadystatechange', function(){
    if (document.readyState == 'complete'){
      Setup();
    } 
  });
} 



</script>