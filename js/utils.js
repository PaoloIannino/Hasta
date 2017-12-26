function ReportError(err_msg){
	var err_header_div = "<div id=\"error_header\">";
	var err_header_msg = "Ops, an error occurred";
	var err_div = "<div id=\"error\">";	
	$("#main").html(err_header_div + err_header_msg + err_div + err_msg + "</div></div");
};

function msieversion()
{
   var ua = window.navigator.userAgent;
   var msie = ua.indexOf ( "rv:11" );

   if ( msie > 0 )      // If Internet Explorer, return version number
      return true;
   else                 // If another browser, return 0
      return false;

};