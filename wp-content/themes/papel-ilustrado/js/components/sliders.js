export function allSliders () {

   // HOME SLIDER
   $(".hero__slider").slick({
      infinite: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      dots: false,
      arrows: true,
      // fade: true,
      responsive: [
         {
            breakpoint: 770,
            settings: {
               arrows: false,
            },
         },
      ],
   });
   $(".paiting__slider").slick({
      infinite: true,
      slidesToShow: 4,
      slidesToScroll: 1,
      dots: true,
      arrows: true,
      responsive: [
         {
            breakpoint: 770,
            settings: {
               slidesToShow: 3,
               slidesToScroll: 1,
               dots: true,
            },
         },
         {
            breakpoint: 480,
            settings: {
               slidesToShow: 1,
               slidesToScroll: 1,
               dots: true,
            },
         },
      ],
   });
   $(".composition__slider").slick({
      infinite: true,
      slidesToShow: 2,
      slidesToScroll: 1,
      dots: true,
      arrows: true,
      responsive: [
         // {
         //     breakpoint: 770,
         //     settings: {
         //         slidesToShow: 1,
         //         slidesToScroll: 1,
         //         dots: true,
         //     },
         // },
         {
            breakpoint: 480,
            settings: {
               slidesToShow: 1,
               slidesToScroll: 1,
               dots: true,
            },
         },
      ],
   });
   $(".picture__slider").slick({
      infinite: true,
      slidesToShow: 5,
      slidesToScroll: 1,
      dots: true,
      arrows: true,
      responsive: [
         {
            breakpoint: 770,
            settings: {
               slidesToShow: 4,
               slidesToScroll: 1,
               dots: true,
            },
         },
         {
            breakpoint: 480,
            settings: {
               slidesToShow: 1,
               slidesToScroll: 1,
               dots: true,
            },
         },
      ],
   });
   $(".instagram__slider").slick({
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 1,
      dots: true,
      arrows: true,
      responsive: [
         {
            breakpoint: 480,
            settings: {
               slidesToShow: 1,
               slidesToScroll: 1,
               dots: true,
            },
         },
      ],
   });

   // PAGE SLIDER
   $(".fiveColumn__slider").slick({
      infinite: true,
      slidesToShow: 5,
      slidesToScroll: 1,
      dots: true,
      arrows: true,
      responsive: [
         {
            breakpoint: 770,
            settings: {
               slidesToShow: 4,
               slidesToScroll: 1,
               dots: true,
            },
         },
         {
            breakpoint: 480,
            settings: {
               slidesToShow: 1,
               slidesToScroll: 1,
               dots: true,
            },
         },
      ],
   });
   $(".fourColumn__slider").slick({
      infinite: true,
      slidesToShow: 4,
      slidesToScroll: 1,
      dots: true,
      arrows: true,
      responsive: [
         {
            breakpoint: 770,
            settings: {
               slidesToShow: 3,
               slidesToScroll: 1,
               dots: true,
            },
         },
         {
            breakpoint: 480,
            settings: {
               slidesToShow: 1,
               slidesToScroll: 1,
               dots: true,
            },
         },
      ],
   });
   // Slider syncing

   $(".slider-for").slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: false,
      fade: true,
      asNavFor: ".slider-nav",
      responsive: [
         {
            breakpoint: 480,
            settings: {
               slidesToShow: 1,
               slidesToScroll: 1,
               dots: true,
            },
         },
      ],
   });
   $(".slider-nav").slick({
      slidesToShow: 3,
      slidesToScroll: 1,
      asNavFor: ".slider-for",
      dots: true,
      centerMode: true,
      focusOnSelect: true,
      responsive: [
         {
            breakpoint: 480,
            settings: {
               slidesToShow: 1,
               slidesToScroll: 1,
               dots: true,
            },
         },
      ],
   });
}