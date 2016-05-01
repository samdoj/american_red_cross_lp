$('#accordion').on('shown.bs.collapse', function (e) {

  // Validate this panel belongs to this accordian, and not an embedded one
  var actualAccordianId = $('a[href="#' + $(e.target).attr('id') + '"').data('parent');
  var targetAccordianId = '#' + $(this).attr('id');
  if (actualAccordianId !== targetAccordianId) return;

  var clickedHeader = $(this).find('.panel > .collapse.in').closest('.panel').find('.panel-heading');
  var offset = clickedHeader.offset();
  var top = $(window).scrollTop();
  if(offset) {
    var topOfHeader = offset.top;
    if(topOfHeader < top) {
      $('html,body').animate({ scrollTop: topOfHeader}, 50, 'swing');
    }
  }
});