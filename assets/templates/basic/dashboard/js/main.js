"user strict";

// Preloader
$(window).on("load", function () {
    $(".preloader").fadeOut(1000);
});

//Menu Dropdown
$("ul>li>.sub-menu").parent("li").addClass("has-sub-menu");

$(".menu li a").on("click", function () {
    var element = $(this).parent("li");
    if (element.hasClass("open")) {
        element.removeClass("open");
        element.find("li").removeClass("open");
        element.find("ul").slideUp(300, "swing");
    } else {
        element.addClass("open");
        element.children("ul").slideDown(300, "swing");
        element.siblings("li").children("ul").slideUp(300, "swing");
        element.siblings("li").removeClass("open");
        element.siblings("li").find("li").removeClass("open");
        element.siblings("li").find("ul").slideUp(300, "swing");
    }
});

// Responsive Menu
var headerTrigger = $(".header-trigger");
headerTrigger.on("click", function () {
    $(".menu").toggleClass("active");
    $(".overlay").toggleClass("active");
});

// Overlay Event
var over = $(".overlay");
over.on("click", function () {
    $(".overlay").removeClass("active");
});

// Sticky Menu
var header = document.querySelector(".header");
if (header) {
    window.addEventListener("scroll", function () {
        header.classList.toggle("sticky", window.scrollY > 0);
    });
}

$(".sidebar-menu li a").on("click", function (e) {
    $(".sidebar-submenu").removeClass("active");
    $(this).siblings(".sidebar-submenu").toggleClass("active");
});

$('.bg-img').css('background', function () {
    var bg = 'url(' + $(this).data('background-image') + ')';
    return bg;
});

$(".sidebar-submenu").parent().addClass("has-submenu");

let elem = document.documentElement;
/* View in fullscreen */
function openFullscreen() {
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.mozRequestFullScreen) {
        /* Firefox */
        elem.mozRequestFullScreen();
    } else if (elem.webkitRequestFullscreen) {
        /* Chrome, Safari and Opera */
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
        /* IE/Edge */
        elem.msRequestFullscreen();
    }
}

/* Close fullscreen */
function closeFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.mozCancelFullScreen) {
        /* Firefox */
        document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
        /* Chrome, Safari and Opera */
        document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
        /* IE/Edge */
        document.msExitFullscreen();
    }
}

$(".fullscreen-open").on("click", function () {
    $(this).addClass("d-none");
    $(".fullscreen-close").addClass("d-grid");
    $(".fullscreen-close").removeClass("d-none");
});
$(".fullscreen-close").on("click", function () {
    $(this).addClass("d-none");
    $(".fullscreen-open").addClass("d-grid");
    $(".fullscreen-open").removeClass("d-none");
});

$(".dash-sidebar-toggler").on("click", function () {
    $(".dashboard-sidebar").toggleClass("active");
});

$(".dash-sidebar-close").on("click", function () {
    $(".dashboard-sidebar").removeClass("active");
});

let clickOne = document.getElementById("dashboard-sidebar");
if (clickOne) {
    $("body").on("click", function (e) {
        let clickTwo = document.getElementById("dash-sidebar-toggler");

        if (clickOne !== e.target && !clickOne.contains(e.target) && clickTwo !== e.target && !clickTwo.contains(e.target)) {
            $(".dashboard-sidebar").removeClass("active");
        }
    });
}

$(".search-toggler").on("click", function (e) {
    $(".search-form").toggleClass("active");
});

// var current = location.pathname.split("/")[1];
// $(".sidebar-menu li a").each(function () {
//     if ($(this).attr("href").indexOf(current) !== -1 && current != "") {
//         $(this).addClass("active");
//     }
// });

function copyText() {
    var copyText = document.getElementById("referralURL");
    var copyText = document.getElementById("ref-url");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
}

//Faq
$(".faq-item__title").on("click", function (e) {
    var element = $(this).parent(".faq-item");
    if (element.hasClass("open")) {
        element.removeClass("open");
        element.find(".faq-item__content").removeClass("open");
        element.find(".faq-item__content").slideUp(300, "swing");
    } else {
        element.addClass("open");
        element.children(".faq-item__content").slideDown(300, "swing");
        element.siblings(".faq-item").children(".faq-item__content").slideUp(300, "swing");
        element.siblings(".faq-item").removeClass("open");
        element.siblings(".faq-item").find(".faq-item__content").slideUp(300, "swing");
    }
});

$.each($('input, select, textarea'), function (i, element) {
    if (element.hasAttribute('required')) {
        $(element).closest('.form-group').find('label').first().addClass('required');
    }
});

$('.export-item').on('change', function (e) {
    let value = $(this).val();
    if (value == 'custom') {
        let item = prompt('How many items interested to export');
        item = parseInt(item);
        if (!item) {
            $(this).find(`option[value=10]`).attr('selected', true);
            return false;
        }
        if (isNaN(item)) {
            notify('error', "Only a number is allowed");
            return false;
        }

        let maxItem = parseInt($(this).attr('max-item') || 100);

        if (item >= maxItem) {
            notify('error', `Max export item is ${maxItem}`);
            item = maxItem;
        }

        let items = [
            10,
            50,
            100,
            maxItem
        ];


        if (items.indexOf(item) == -1) {
            items.push(item)
        }

        let option = "";

        items.forEach(element => {
            option += `<option value="${element}" ${element == item ? 'selected' : ''}>${element}</option>`
        });
        option += `<option value="custom">Custom</option>`;
        $(this).html(option)
    }
});

function setFormValue(data, formId, replaceData = null) {
    let form = document.getElementById(formId);
    let elements = form.querySelectorAll("input, select, textarea");

    Array.from(elements).forEach(element => {
        let elementName = element.getAttribute('name');
        let elementType = element.getAttribute('type');

        if (elementType != "hidden" && elementType != 'password' && elementType != 'checkbox') {
            if (replaceData != null && typeof replaceData == 'object' && replaceData[elementName]) {
                elementName = replaceData[elementName];
            }

            let value = data[elementName] || '';
            element.value = value;

            if (element.tagName.toLowerCase() === 'select' && $(element).hasClass('select2--dropdown')) {
                if (!value) {
                    let defaultOption = $(element).find('option:first').val();
                    $(element).val(defaultOption).trigger('change');
                } else {
                    $(element).val(value).trigger('change');
                }
            }
        }

        if (elementType == 'password') {
            element.closest('.form-group').classList.add('d-none');
        }
    });
}

function select2ReInitialization() {
    $.each($('.select2--dropdown'), function () {

        let firstOptionValue = $(this).find('option:first').val();
        $(this).val(firstOptionValue);

        $(this)
            .wrap(`<div class="position-relative"></div>`)
            .select2({
                dropdownParent: $(this).parent(),
                width: "100%"
            });
    });
}