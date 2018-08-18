/**
 * Created by hw on 2018/5/31.
 */
/*导航栏*/
$(".menu li a").wrapInner('<span class="out"></span>');
$(".menu li a").each(function () {
    $('<span class="over">' + $(this).text() + '</span>').appendTo(this);
});
$(".menu li a").hover(function () {
    $(".out", this).stop().animate({'top': '48px'}, 300);
    $(".over", this).stop().animate({'top': '0px'}, 300);
}, function () {
    $(".out", this).stop().animate({'top': '0px'}, 300);
    $(".over", this).stop().animate({'top': '-48px'}, 300);
});