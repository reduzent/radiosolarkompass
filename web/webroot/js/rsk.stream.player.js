$(document).ready(function(){

  // Local copy of jQuery selectors, for performance.
  var jpPlayTime = $("#jplayer_play_time");
  var jpTotalTime = $("#jplayer_total_time");
  var jpStatus = $("#demo_status"); // For displaying information about jPlayer's status in the demo page

  $("#jquery_jplayer").jPlayer({
    ready: function () {
      this.element.jPlayer("setFile", "http://195.176.254.176:8010/rsk.mp3").jPlayer("stop");
      demoInstanceInfo(this.element, $("#demo_info")); // This displays information about jPlayer's configuration in the demo page
    },
    solution: "html, flash",
    swfPath: "js"
  })
  .jPlayer("onProgressChange", function(loadPercent, playedPercentRelative, playedPercentAbsolute, playedTime, totalTime) {
    jpPlayTime.text($.jPlayer.convertTime(playedTime));
    jpTotalTime.text($.jPlayer.convertTime(totalTime));
    demoStatusInfo(this.element, jpStatus); // This displays information about jPlayer's status in the demo page
  })
  .jPlayer("onSoundComplete", function() {
    this.element.jPlayer("play");
  });
});

