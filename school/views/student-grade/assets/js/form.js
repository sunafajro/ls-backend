$(function() {
  'use strict';

  var contentsBlock = $(".js--exam-contents");
  var loading = false;

  function setFieldsData(items) {
    if (!loading) {
      items.forEach(function(item) {
        $('input[name="' + item.name + '"]')
          .val(item.value)
          .trigger("change");
      });
    } else {
      setTimeout(function () {
        return setFieldsData(items)
      }, 100);
    }
  }

  $(".js--exam-select").on("change", function(e) {
    var _this = $(this);
    contentsBlock.html("");
    if (e.target.value) {
      loading = true;
      $.get(_this.data('url') + '?exam=' + e.target.value, {}, function(
        data
      ) {
        if (
          data.hasOwnProperty("contents") &&
          typeof data.contents === "object" &&
          Object.keys(data.contents).length
        ) {
          for (var key in data.contents) {
            var col = Object.keys(data.contents).length
              ? Math.floor(12 / Object.keys(data.contents).length)
              : null;
            var templateInput = $("#template-input").clone();
            if (col) {
              templateInput.addClass("col-sm-" + col);
            }
            templateInput.removeClass("hidden");
            var inputField = templateInput.find("input");
            inputField.prop("name", "StudentGrade[contents][" + key + "]");
            inputField.prop("placeholder", data.contents[key]);
            contentsBlock.append(templateInput);
          }
        } else {
          var alertBlock = $("<div>", {
            "class": "alert alert-danger",
            "text": "Не удалось получить содержание экзамена"
          });
          contentsBlock.append(alertBlock);
        }
        loading = false;
      });
    }
  });
  $(".js--edit-attestation").on("click", function() {
    var _this = $(this);
    var mainParams = _this.data("main-params");
    var scoreContents = _this.data("score-contents");
    mainParams.forEach(function(item) {
      $("#" + item.id)
        .val(item.value)
        .trigger("change");
    });
    setTimeout(function () {
      return setFieldsData(scoreContents)
    }, 100);
    $('.js--attestations-form .js--create-button,.js--update-button').each(function () {
      var _this = $(this);
      if (_this.hasClass('js--create-button')) {
        _this.addClass('hidden');
      } else {
        _this.removeClass('hidden');
      }
    });
    var $form = $('.js--attestations-form');
    $form.prop('action', _this.data('action-url'));
  });
});
