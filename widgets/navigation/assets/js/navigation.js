$(function() {
  var $rootBlock = $("#navigation-panel");
  var startTime = $rootBlock.data("limit-time") * 60;
  var $counterBlock = $("#navigation-timer");

  function decrementTime() {
    startTime--;
    if (startTime === 0) {
      logout();
    }
    $counterBlock.text(timeFormat(startTime));
    setTimeout(decrementTime, 1000);
  }

  function timeFormat(time) {
    $minutes = ~~(time / 60);
    $seconds = time % 60;
    return (
      ($minutes < 10 ? "0" + $minutes : $minutes) +
      ":" +
      ($seconds < 10 ? "0" + $seconds : $seconds)
    );
  }

  function logout() {
    $.ajax({
      method: "POST",
      url: $rootBlock.data("logout-url")
    }).done(function() {
      window.location.reload();
    });
  }

  $(document).ready(function() {
    decrementTime();
    var $navMessageModal = $("#navigation-message-modal");
    if ($navMessageModal.length) {
      $navMessageModal.modal("show");
    }
  });
});
