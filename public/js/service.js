function hideModalMsg() {
	$('#alertModal').modal('hide');
}

function showErrorMsg(msg, timeout) {
	$('#alertModal_contentbox').removeClass();
	$('#alertModal_contentbox').addClass("alert alert-danger alert-dismissible");
	$('#alertModal_contentbox').html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Coś poszło nie tak!</strong> ' + msg);
	$('#alertModal').modal('show');
		
	if (timeout > 0) window.setTimeout("hideModalMsg()", timeout);
}

function showSuccesMsg(msg, timeout) {
	$('#alertModal_contentbox').removeClass();
	$('#alertModal_contentbox').addClass("alert alert-success alert-dismissible");
	$('#alertModal_contentbox').html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Powodzenie!</strong> ' + msg);
	$('#alertModal').modal('show');
	
	if (timeout > 0) window.setTimeout("hideModalMsg()", timeout);
}