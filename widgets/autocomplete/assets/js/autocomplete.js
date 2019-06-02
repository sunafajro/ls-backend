$(document).ready(function() {
  var foundStudents = [];
  var $studentField = $("#js--autocomplete-hidden");
  var $studentListBlock = $("#js--autocomplete-list");
  $("#js--autocomplete")
    .on("input", function() {
      var $this = $(this);
      var str = $this.val();
      if (str.length >= $this.data("min-length")) {
        $.ajax({
          method: "POST",
          url: $this.data("url"),
          data: { term: str }
        }).done(function(result) {
          $studentListBlock.html("");
          if (Array.isArray(result) && result.length) {
            foundStudents = result;
            foundStudents.forEach(function(student) {
              $studentListBlock.append(
                '<li data-id="' +
                  student["value"].trim() +
                  '">' +
                  student["label"].trim() +
                  "</li>"
              );
            });
            $studentListBlock.show();
            $("#js--autocomplete-list li").on("click", function() {
              var $this = $(this);
              $studentField.val($this.data("id"));
              $("#js--autocomplete").val($this.text());
            });
          } else {
            $studentListBlock.hide();
          }
        });
      }
    })
    .on("focusout", function() {
      var $this = $(this);
      setTimeout(function() {
        $studentListBlock.hide();
        $studentListBlock.html("");
      }, 200);
    });
});
