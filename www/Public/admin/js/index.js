$(function () {
    $(".content-left,.content-right").height($(window).height() - 130);
    $("#collapseOne>a").on("click",function(){
        $(this).addClass("cur");
        $(this).siblings("a").removeClass("cur");
    });
});
//$(".accordion-inner").click(function () {
//    $(".active").html($(this).find(".left-body").text());
//});
//
//$(window).resize(function () {
//    $(".content-left,.content-right").height($(window).height() - 130);
//});
