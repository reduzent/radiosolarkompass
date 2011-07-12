  var delay = 120;
  var num = 12;
  var current = 0;
  var urlbase = 'pix/aussenden_';
  function timeMsg() {
    var t=setTimeout("timeMsg()",delay);
    current = (current + 1) % num;
    var aussenden = document.getElementById("aussenden");
    if (aussenden != null) {
      aussenden.src = urlbase + (current + 1) + '.png';
    }
  }

