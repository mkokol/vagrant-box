$(document).ready(function(){
    loadAlbums();
});

function updateFormPlaseHolder(imgName) {
    mboxRemove();
    var albumId = ($("#currentAlbumId").length > 0) ? $("#currentAlbumId").val() : 0;
    loadAlbum(albumId);
}

function viewAlbum (albumId) {
    loadAlbum(albumId);
}
function viewAlbums () {
    
}
function loadAlbums(){
    $("#userImages").load(
        baseUrl + "/user/albums?v=" + baseVersion,
        function(){ reloadBox(); }
    );
}
function loadAlbum(albumId){
    $("#userImages").load(
        baseUrl + "/user/images?albumId=" + albumId + "&v=" + baseVersion,
        function(){ reloadBox(); }
    );
}