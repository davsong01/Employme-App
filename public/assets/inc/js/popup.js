$(document).ready(function ()
{
	//Fade in delay for the background overlay (control timing here)
	$("#bkgOverlay").delay(60).fadeIn(400);
  //Fade in delay for the popup (control timing here)
	$("#delayedPopup").delay(60).fadeIn(400);
	
	//Hide dialouge and background when the user clicks the close button
	$("#btnClose").click(function (e)
	{
		HideDialog();
		e.preventDefault();
	});
});
//Controls how the modal popup is closed with the close button
function HideDialog()
{
	$("#bkgOverlay").fadeOut(400);
	$("#delayedPopup").fadeOut(300);
}