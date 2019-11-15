jQuery( function ( $ ) {

  // First we have to add the class to the DOM
  $( ".wpinventory_filter span.sort, .wpinventory_filter span.categories" ).addClass( 'dropdown-select' );
  //$(".wpim-asf-values select").parent().addClass("dropdown-select");

  var x, i, j, selElmnt, a, b, c;
  /* Look for any elements with the class "dropdown-select": */
  x = document.getElementsByClassName( "dropdown-select" );
  for ( i = 0; i < x.length; i++ ) {
    selElmnt = x[ i ].getElementsByTagName( "select" )[ 0 ];
    /* For each element, create a new DIV that will act as the selected item: */
    a = document.createElement( "DIV" );
    a.setAttribute( "class", "select-selected" );
    a.innerHTML = selElmnt.options[ selElmnt.selectedIndex ].innerHTML;
    x[ i ].appendChild( a );
    /* For each element, create a new DIV that will contain the option list: */
    b = document.createElement( "DIV" );
    b.setAttribute( "class", "select-items select-hide" );
    for ( j = 1; j < selElmnt.length; j++ ) {
      /* For each option in the original select element,
      create a new DIV that will act as an option item: */
      c           = document.createElement( "DIV" );
      c.innerHTML = selElmnt.options[ j ].innerHTML;
      c.addEventListener( "click", function ( e ) {
        /* When an item is clicked, update the original select box,
        and the selected item: */
        var y, i, k, s, h;
        s = this.parentNode.parentNode.getElementsByTagName( "select" )[ 0 ];
        h = this.parentNode.previousSibling;
        for ( i = 0; i < s.length; i++ ) {
          if ( s.options[ i ].innerHTML == this.innerHTML ) {
            s.selectedIndex = i;
            h.innerHTML     = this.innerHTML;
            y               = this.parentNode.getElementsByClassName( "same-as-selected" );
            for ( k = 0; k < y.length; k++ ) {
              y[ k ].removeAttribute( "class" );
            }
            this.setAttribute( "class", "same-as-selected" );
            break;
          }
        }
        h.click();
      } );
      b.appendChild( c );
    }
    x[ i ].appendChild( b );
    a.addEventListener( "click", function ( e ) {
      /* When the select box is clicked, close any other select boxes,
      and open/close the current select box: */
      e.stopPropagation();
      closeAllSelect( this );
      this.nextSibling.classList.toggle( "select-hide" );
      this.classList.toggle( "select-arrow-active" );
    } );
  }

  function closeAllSelect( elmnt ) {
    /* A function that will close all select boxes in the document,
    except the current select box: */
    var x, y, i, arrNo = [];
    x                  = document.getElementsByClassName( "select-items" );
    y                  = document.getElementsByClassName( "select-selected" );
    for ( i = 0; i < y.length; i++ ) {
      if ( elmnt == y[ i ] ) {
        arrNo.push( i )
      } else {
        y[ i ].classList.remove( "select-arrow-active" );
      }
    }
    for ( i = 0; i < x.length; i++ ) {
      if ( arrNo.indexOf( i ) ) {
        x[ i ].classList.add( "select-hide" );
      }
    }
  }

  /* If the user clicks anywhere outside the select box,
  then close all select boxes: */
  document.addEventListener( "click", closeAllSelect );

  // Flyout sidebar
  $( 'section.main_content span.sidebar_flyout' ).click( function () {
    $( this ).toggleClass( 'open' );
  } );

  // We want the content and the sidebar to be equal height
  var article_height = $("section.main_content article").height();
  var aside_height = $("section.main_content aside").height();

  if (article_height < aside_height) {
    $("section.main_content").css({height: aside_height + "px"})
  }

} );