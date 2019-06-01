$(document).ready(function() {
  var loading = false;
  var foundStudents = [];
  var $studentField = $("#js--autocomplete-hidden");
  var $studentListBlock = $("#js--autocomplete-list");
  $("#js--autocomplete")
    .on("input", function() {
      var $this = $(this);
      var str = $this.val();
      if (!loading) {
        loading = true;
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
          loading = false;
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
