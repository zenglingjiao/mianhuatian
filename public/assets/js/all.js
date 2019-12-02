"use strict";

// es5 area
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    var active = getUrlParam('active');
    if (active == "1") {
        $("html, body").animate({ scrollTop: $("#login_fp").offset().top }, 'slow');
        //return false;
    }
    if (active == "2") {
        $("html, body").animate({ scrollTop: $("#awards").offset().top }, 'slow');
        //return false;
    }


    $('.alert-login').on("click", function (event) {
        $('#mobel_demo').modal('show');
    });
    $('.attention').on("click", function (event) {
        $('#attention').modal('show');
    });
    $('.scroll-login').on("click", function (event) {
        if ($("#login_fp").length > 0) {
            $('html,body').animate({ scrollTop: $("#login_fp").offset().top }, 1200);
        }
        else {
            window.location.replace("index.html?active=1");
        }
        
    });
    $('.awards').on("click", function (event) {
        if ($("#awards").length > 0) {
            $('html,body').animate({ scrollTop: $("#awards").offset().top }, 1200);
        }
        else {
            window.location.replace("index.html?active=2");
        }
       
    });

    $('.determine').on("click", function (event) {
        $('#determine').modal('show');
    });
    $(document).on('click', '.redb .p_img', function () {
        var $this = $(this);
        var $z = $this.attr("data-z");
        var $title = $this.attr("data-title");
        if ($z == "1") {
            $("#luck .model_title span").text($title);
            $(".ad_title").hide();
        } else if ($z == "2") {
            $("#luck .model_title span").text($title);
            $(".ad_title").show();
        } else {
            $(".ad_title").hide();
        }
        $('#luck').modal('show');
    });
    new WOW().init();

})

//获取url中的参数
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}

//var yuanwidth = $(window).width();
//$(window).on('resize', function () {
//    console.log($(window).width());
//    //window.location.reload();
//    if (yuanwidth != $(window).width()) {
//        location.reload();
//        console.log(0);
//        return;
//    }

//});