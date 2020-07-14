jQuery(document).ready(function() {
	jQuery('.fancybox').fancybox({
    afterLoad : function() {
      var caption = jQuery(this.element).data("caption") ? '<br />' + jQuery(this.element).data("caption") : '';
      this.title = this.title ? '<strong>' + this.title + '</strong>' + caption : '';
    },
    arrows: false,
    helpers: {
      title : {
        type : 'over',
        position : 'top'
      }
    },
    margin: 40,
    openEffect : 'elastic',
    padding: 0,
    scrollOutside: false,
  });
});
