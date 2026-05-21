$('document').ready(function() {

new WOW().init();


$('.hero-slider').owlCarousel({
    stagePadding: 100,
    loop:true,
    margin:20,
    nav:false,
    dots: false,
    responsive:{
        0:{
            items:1,
            stagePadding: 10,
            margin: 5,
        },
        600:{
            items:1,
            stagePadding: 20,
            margin: 10,

        },
        1000:{
            items:1,
            stagePadding: 50,

        }
    }
});


$('.category-slider').owlCarousel({
    // stagePadding: 100,
    loop:true,
    margin:20,
    items: 6,
    nav:false,
    dots: false,
    responsive:{
        0:{
            items:3
        },
        600:{
            items:3
        },
        1000:{
            items:6
        }
    }
});




$('.holiday-slider').owlCarousel({
    stagePadding: 0,
    margin:15,
    loop:true,
    nav:true,
    dots: false,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:1
        }
    }
});

$('.review-slider').owlCarousel({
    // stagePadding: 100,
    margin:15,
    loop:true,
    nav:false,
    items:6,
    dots: true,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:2,
            stagePadding: 20,
            margin:10,
        },
        1000:{
            items:3
        }
    }
});


$('.arrival-slider').owlCarousel({
    loop: true,
    margin: 10,
    autoplayTimeout: 2500,
    responsiveClass: true,
    nav:true,
    dots: false,
    
    responsive: {
    0: {
    items: 2,
    autoplay:true
    },
    600: {
    items: 3,
    autoplay:true
    },
    1000: {
    items: 4,
    autoplay:true,
    loop: true,
    margin: 20
}
}

});


});