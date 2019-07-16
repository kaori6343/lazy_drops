window.addEventListener('DOMContentLoaded',
function(){

  var node = document.querySelector('#count-text');

  node.addEventListener('keyup', function(){

    var count = this.value.length;

    var counterNode = document.querySelector('.show-count-text');

    counterNode.innerHTML = count;

  },false);

},false
);
