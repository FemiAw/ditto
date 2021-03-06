function getCaption(albumId, filename) {
    // get the edited input value
    var value = document.body.getElementsByClassName('caption-container')[0].getElementsByTagName('input')[0].value;
    // create an XMLHTTPRequest to the php function.
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE) {
            if (this.status === 200) {
                // executed when the request is successful
                document.body.getElementsByClassName('caption-container')[0].innerHTML = this.responseText; // should get the new caption.
            } else if (this.status === 400) {
                console.log("There was an error 400");
            } else {
                console.log("Something else other than 200 or 400 was returned.");
            }
        }
    }
    var querystring = '?filename=' + filename;

    xmlhttp.open('POST', '/php/photos/get_photo_caption.php' + querystring, true);
    xmlhttp.send();
};
