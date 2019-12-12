"use strict";

// es5 area
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    var active = getUrlParam('active');
    var wow=getUrlParam('wow');
    var quey=getUrlParam('quey');
    var redpackage=getUrlParam('redpackage');
    if(wow=="2"){
        $("html, body").animate({ scrollTop: $("#winlist").offset().top }, 'slow');
    }
    if(quey=="2"){
        $("html, body").animate({ scrollTop: $("#quey").offset().top }, 'slow');
    }
    if(redpackage=="2"){
        $("html, body").animate({ scrollTop: $("#redpackage").offset().top }, 'slow');
    }
    if (active == "1") {
        $("html, body").animate({ scrollTop: $("#login_fp").offset().top }, 'slow');
        //return false;
    }
    if (active == "2") {
        $("html, body").animate({ scrollTop: $("#awards").offset().top }, 'slow');
        // $("html, body").animate({ scrollTop: $("#winlist").offset().top }, 'slow');
        //return false;
    }


    $('.alert-login').on("click", function (event) {
        $('#mobel_demo').modal('show');
    });
    $('.attention').on("click", function (event) {
        $('#attention').modal('show');
    });
    // $('.scroll-login').on("click", function (event) {
    //     if ($("#login_fp").length > 0) {
    //         $('html,body').animate({ scrollTop: $("#login_fp").offset().top }, 1200);
    //     }
    //     else {
    //         window.location.replace("index.html?active=1");
    //     }
    //
    // });
    $('.awards').on("click", function (event) {
        if ($("#awards").length > 0) {
            $('html,body').animate({ scrollTop: $("#awards").offset().top }, 1200);
        }
        else {
            window.location.replace("index.html?active=2");
        }
       
    });
    $('.winlist').on("click", function (event) {
        if ($("#winlist").length > 0) {
            $('html,body').animate({ scrollTop: $("#winlist").offset().top }, 1200);
        }
        else {
            window.location.replace("winlist.html?wow=2");
        }
    });
    $('.query').on("click", function (event) {
        if ($("#quey").length > 0) {
            $('html,body').animate({ scrollTop: $("#quey").offset().top }, 1200);
        }
        else {
            window.location.replace("query.html?quey=2");
        }
    });


    $('.determine').on("click", function (event) {
        $('#determine').modal('show');
    });
    $(document).on('change', '[name="classd1"]', function () {
        $('[name="phone1"]').attr('placeholder', $(this).attr("data-placeholder"));
    });
    $(document).on('change', '[name="classd"]', function () {
        $('[name="phone"]').attr('placeholder', $(this).attr("data-placeholder"));
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


    $(document).on('click', '.btnNext', function () {
        var $this = $(this);
        var content = $(this).closest('div.tab_list');
        var $index = content.attr("data-index");
        content.removeClass('active');
        content.removeClass('animated fadeIn');
        content.next().addClass("animated fadeIn active");
        if ($index == "2") {
            //$("#form").submit();

            ///
            var jumphref = $(".jump").attr("data-href");
            setTimeout(function () {
                window.location.replace(jumphref);
            }, 3000);
        }
    });
    
    new WOW().init();

})

//��ȡurl�еĲ���
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //����һ������Ŀ�������������ʽ����
    var r = window.location.search.substr(1).match(reg);  //ƥ��Ŀ�����
    if (r != null) return unescape(r[2]); return null; //���ز���ֵ
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