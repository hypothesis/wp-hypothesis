var anchors = document.getElementsByTagName('a');
var re = /wp-content\/uploads[.+]\.pdf/;
for ( i=0; i<anchors.length; i++ ) {
   var href = anchors[i].href;
   if ( href.match(/wp-content\/uploads.+\.pdf/i) ) 
       anchors[i].href = 'https://via.hypothes.is/' + anchors[i].href;
  }
