/**
 * micko
 */
//var carrentPage = 1;
//$(document).ready(function(){
//    showImageOnPage(1);
//});
//function showImageOnPage(page){
//    carrentPage = page;
//    $("#photoList").load(
//        baseUrl + "/admin/photolist?page=" + page,
//        function(){
//            $('.paging').click(function (){
//                var pageNumner = $(this).attr('data-page-number');
//                showImageOnPage(pageNumner);
//                return false;
//            });
//        }
//    );
//}
//function deleteImage(id, imgId, imgName){
//    $.ajax({
//        type: "POST"
//        , url: baseUrl + "/admin/photodelete?v=" + baseVersion
//        , data: {
//            id: id
//            , imgId: imgId
//            , imgName: imgName
//        }
//        , success: function(data) {
//            //showImageOnPage(carrentPage);
//        }
//    });
//}
//
////TO DO: check
//function updateFormPlaseHolder(imgName) {
//    mboxRemove();
//    $("#photoList").load(baseUrl + "/admin/photolist?createdBy=adminForContent&product=" + $("#produseTovEl").val() + "&theme=" + $("#produseTovTheme").val());
//}
