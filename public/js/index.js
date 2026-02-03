
$(document).ready(function () {



    const input = $('.inputfield');

    const totalguests = $('.totalguests');



    $(".adults").each(function (index) {
        $('.incdec').on("click", function (event) {

            event.stopImmediatePropagation();
            let value = 0;
            value = $(this).siblings(input).next().val();

            ++value;
            $(this).siblings(input).next().val(value);

            if (event.currentTarget.id.includes("children")) {
                if (value == 1) {
                    $("#childrenDetails").append(`<div class="mb-25" style="border-top:1px solid #707070;">
                        <p class="person px-24" style="margin-top:20px;">AGE</p>
                    </div>`);
                }
                $("#childrenDetails").append(`<div
                    class="adults px-24 counter d-flex justify-content-between align-items-center mb-25">
                    <div>
                        <p class="person">CHILD ${value}</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <select name="age" id="age">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                </div>`);
            }

            var sum = 0;
            $('.inputfield').each(function () {
                sum += +$(this).val();
            });

            $(".totalguests").val(sum);

            $(".guest").text(sum);



        });
    });

    $(".adults").each(function (index) {
        $('.decrement').on("click", function (event) {
            event.stopImmediatePropagation();
            let value = 0;
            value = $(this).siblings(input).val();

            --value;
            $(this).siblings(input).val(value);
            if (event.currentTarget.id.includes("children")) {
                $("#childrenDetails").children("div:last").remove();
                if (value == 0) {
                    $("#childrenDetails").empty();
                }
            }


            var sum = 0;
            $('.inputfield').each(function () {
                sum += +$(this).val();
            });
            $(".totalguests").val(sum);

        });
    });




    $('.dropdown-custom-toggle').click(function (e) {
        e.preventDefault();
        $('.dropdown-menu').addClass('show');
        $('.dropdown-custom').addClass('active');
        $('.t-datepicker-day').remove()
        $('.t-datepicker').removeClass('t-datepicker-open');


    });


    $('.search-filter').click(function (e) {
        e.preventDefault();
        $(this).removeClass('remove-highlight');
    });
    $('.dropdown-custom').click(function (e) {
        e.preventDefault();
        $('.search-filter').removeClass('remove-highlight');
    });

    $(window).click(function () {
        $('.dropdown-menu').removeClass('show');
        $('.dropdown-custom').removeClass('active');
    });

    $('.dropdown-custom').click(function (event) {
        event.stopPropagation();
    });

    // $('.search-filter').click(function (event) {
    //     event.stopPropagation();
    // });



});






$(document).click(function (event) {
    var $target = $(event.target);
    if (!$target.closest('.search-filter').length) {
        $('.search-filter').addClass('remove-highlight');
    }
});








const minus = $('.roomsdecrement');
const input = $('.roominputfield');

// const adultincrement = $('.adultincrement')
// const childreincrement = $('.childreincrement')
// var totalPoints = childronnum + adultrnum;
// var childronnum = 0;
// var adultrnum = 0;
// const totalguests = $('.totalguests');
// // const plus = $('.increment');
// // const reset = $('.Reset');
// // const apply = $('.apply');
// // const totalguests = $('.totalguests');
const roomincrement = $('.roomincrement')
const inputrooms = $('#inputRooms');
const roomcount = $('.roomcount');

roomincrement.click(function () {
    var value = $(this).siblings(input).val();
    value++;
   // console.log(value);

    $(this).siblings(input).val(value);
    $('.roomcount').text(value)
})

// input.text(0);

minus.click(function () {

    var value = $(this).siblings(input).val();
    if (value > 1) {
        value--;
    }
    $(this).siblings(input).val(value);
    $('.roomcount').text(value)
});







$(document).ready(function () {
    $('input').val('')
});

$('.bloglistingcarousel').each(function () {
    var slider = $(this);
    slider.slick({
        dots: true,
        autoplay: true,
        autoplaySpeed: 5000,
        mobileFirst: true,
        arrows: false,
        responsive: [{
            breakpoint: 480,
            settings: "unslick"
        }]
    });

});

$(function () {
    $('input[name="datefilter"]').daterangepicker({
        autoUpdateInput: false,
        "opens": "center",
        "autoApply": true,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('.checkin input').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY'));
        $('#checkout').val(picker.endDate.format('MM/DD/YYYY'));
        $('.checkout').removeClass('active-border');
        $('.checkin').removeClass('active-border');

    });

    $('input[name="datefilter"]').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });




});








// adultincrement.click(function () {
//     var value = $(this).siblings(input).val();
//     value++;
//     $(this).siblings(input).val(value);
//     adultrnum = value;
//     totalPoints = childronnum + adultrnum
//     totalguests.text(totalPoints);
// })
// childreincrement.click(function () {
//     var value = $(this).siblings(input).val();
//     value++;
//     $(this).siblings(input).val(value);
//     childronnum = value;
//     totalPoints = childronnum + adultrnum
//     totalguests.text(totalPoints);
// })


// $(reset).click(function (e) {
//     e.preventDefault();
//     input.val(0)
//     $('#inputRooms').val(0)
//     childronnum.parseFloat($(this).val()) + totalPoints
// });


// $(apply).click(function (e) {
//     e.preventDefault();
//     var totalPoints = 0;
//     $('.counter .inputfield').each(function () {
//         totalPoints = parseFloat($(this).val()) + totalPoints;
//     });
//     totalguests.text(totalPoints - 1);
//     roomcount.text(inputrooms.val())
// });// $('.checkin').click(function (e) {
//     e.preventDefault();
//     $(this).addClass('active-border');
//     $('.checkout').removeClass('active-border');

// });
// $('.checkout').click(function (e) {
//     e.preventDefault();
//     $('.checkin').removeClass('active-border');
//     $(this).removeClass('disabled');
//     $(this).addClass('active-border');
// });


// $('.view-more').click(function (e) {
//     e.preventDefault();
//     $('input[name="datefilter"]').data('daterangepicker').show();

// });

if ($(window).width() < 606) {
    $('.typeahed').click(function (e) {
        e.preventDefault();
        $('.other-sections').hide();
        $(this).addClass('unstyletypehead');
    });

    $(document).click(function (event) {
        var $target = $(event.target);
        if (!$target.closest('.typeahed').length) {
            $('.other-sections').show();
            $('.typeahed').removeClass('unstyletypehead');
        }
    });
}
