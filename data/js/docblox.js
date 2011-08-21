var Docblox = {
  bindHistory: function(el) {
    $(el).bind('click', function() {
      var link = $('<a />').attr('href', this.href)[0];
      parent.jQuery.bbq.pushState({ target: el.target, url: link.pathname, anchor: link.hash});
      return false;
    });
  }
}
