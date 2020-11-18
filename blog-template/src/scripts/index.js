$(document).ready(function(){

  //////////////////////////////////////////////////////////////////////////////
  // Load more (and load initial)

  let postsPerPage = 5;

  function loadMore() {
    $('.post:not(.inactive).hidden:lt('+postsPerPage+')').removeClass('hidden');
    if($('.post:not(.inactive).hidden').length === 0 ){
      $('#load-more').addClass('hidden');
    } else {
      $('#load-more').removeClass('hidden');
    }
  }

  loadMore();

  $('#load-more button').on('click', function(){
    loadMore();
  });

  //////////////////////////////////////////////////////////////////////////////
  // Search

  $('.tag').on('click', function(){
    event.preventDefault();
    var tag = $(this).text().toLowerCase();
    window.location.hash = tag;
    $('#search').val(tag);
    search(tag);
  });

  var hash = window.location.hash.substr(1);
  var q = (hash) ? hash : "";

  if(q.length > 0){
    $('#search').val( q.toLowerCase() );
    search(q);
  }

  $('#search').keyup(function(event) {
    window.location.hash = $(this).val();
    search( $(this).val() );
  }).keydown(function( event ) {
    if (event.which == 13) {
      event.preventDefault();
    }
  });

  function search(q) {

    q = q.toLowerCase();

    if(q.length > 0){
      $('.posts').addClass('searching');
      $('.post').addClass('inactive');

      var els = $('.post').filter(function() {
        return ($(this).text().toLowerCase().indexOf(q) > -1);
      }).removeClass('inactive');

      if($('.post.inactive').length === $('.post').length ){
        $('#no-results').removeClass('hidden');
        $('#load-more').addClass('hidden');
      }

      if($('.post.inactive').length > 0 ){
        $('#load-more').removeClass('hidden');
      } else {
        $('#load-more').addClass('hidden');
      }

    } else {
      history.replaceState(null, null, ' ');
      $('.posts').removeClass('searching');
      $('.post').removeClass('inactive');
      $('#no-results').addClass('hidden');
    }

    $('.post').addClass('hidden');
    loadMore();
  }

  //////////////////////////////////////////////////////////////////////////////
  // Nav shrink

  var lastScroll = 0;
  var scrollingDown = false;

  $(document).on('scroll', function(){
    var winTop = $(window).scrollTop();

    if(winTop >= 40) {
      $('nav').addClass('scrolling');
    } else if(winTop < 40) {
      $('nav').removeClass('scrolling');
    }

    scrollingDown = (winTop > lastScroll && winTop > 0)? true : false; // winTop > 300 &&
    lastScroll = winTop;

    if(scrollingDown && winTop >= 40){
      $('nav, .subheader').addClass('hide');
    } else {
      $('nav, .subheader').removeClass('hide');
    }
  });

  //////////////////////////////////////////////////////////////////////////////
  // Copy to Clipboard

  $('#copy-link').on('click', function(){
    event.preventDefault();
    var text = "https://blog.directus.io/posts/" + $(this).data('id') + "/";
    window.prompt("Copy to clipboard, then press ENTER:", text);
  });

	//////////////////////////////////////////////////////////////////////////////

});
