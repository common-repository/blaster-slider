jQuery(function ($) {

    $.fn.BlasterSlider = function (options) {

        var settings = $.extend({
            animation: 'fade',
            title_color: '#ff0000',
            desc_color: '#ff0000',
            nav: true,
            arrows: true,
            caption: true,
            caption_shadow: false,
            hover_pause: false,
            time: 5000,
            height: 200
        }, options);

        return this.each(function () {

            var self = $(this);
            var slideLength = self.children('li').length;
            var timer = parseInt(settings.time, 10);

            self.addClass('blaster-slider-canvas').wrap("<div class='blaster-slider'></div>");
            self.css('height', settings.height + 'px');


            self.children('li:first').addClass('active');

            if (settings.animation === "slide") {
                self.addClass('slide-animation');
            }
            if (settings.animation === "fade") {
                self.addClass('slide-fade');
            }

            if (settings.arrows === true) {
                self.after('<a class="arrows prev-arrow" href="#"><img src="' + blaster_slider_data.img_path + 'left.svg" alt=""></a><a class="arrows next-arrow" href="#"><img src="' + blaster_slider_data.img_path + 'right.svg" alt=""></a>');
            }
            if (settings.nav === true) {
                self.after('<ul class="slider-nav"></ul>');
            }
//            if (settings.nav === false) {
//                $('.slider-nav').css('display', 'none');
//                $(this).find('.slider-nav').css('display', 'none');
//            }
            var navContainer = self.parent('.blaster-slider').children('.slider-nav');
            for (var i = 0; i < slideLength; i++) {
                navContainer.append('<li></li>');
            }
            navContainer.children('li').eq(0).addClass('active');


            self.children('li').children('img').each(function () {
                var imgUrl = $(this).attr('src');
                $(this).parent('li').css('background-image', 'url(' + imgUrl + ')');
            });

            if (settings.caption === true) {

                $(this).find('.image-overlay h1').css('color', settings.title_color);
                $(this).find('.image-overlay h2').css('color', settings.desc_color);
                
                
//                $(this).find('.image-overlay h1').addClass('w3-animate-top');
//                $(this).find('.image-overlay h1').addClass('w3-animate-opacity');

                if (settings.caption_shadow === true) {
                    $(this).find('.image-overlay h1').css('text-shadow', '0px 4px 3px rgba(0,0,0,0.6), 0px 8px 13px rgba(0,0,0,0.2), 0px 18px 23px rgba(0,0,0,0.2)');
                    $(this).find('.image-overlay h2').css('text-shadow', '0px 4px 3px rgba(0,0,0,0.6), 0px 8px 13px rgba(0,0,0,0.2), 0px 18px 23px rgba(0,0,0,0.2)');
                }
                
                $(this).find('.image-overlay h1').css('top', '20%');
                $(this).find('.image-overlay h2').css('bottom', '20%');
            } else {
                $(this).find('.image-overlay h1').css('visibility', 'hidden');
                $(this).find('.image-overlay h2').css('visibility', 'hidden');
            }

            $('.blaster-slider-container').css('visibility', 'visible');

            var changeSlides = (function (normalDirection) {

                var active = self.children('li.active');
                var activeIndex = self.children('li.active').index();
                var nextDot;
                var activeDot = navContainer.children('li.active');

                var next;
                if (normalDirection === false) {
                    next = self.children('li').eq(activeIndex - 1);

                    if (activeIndex - 1 < 0) {
                        next = self.children('li:last');
                    }

                } else {
                    next = self.children('li').eq(activeIndex + 1);
                    if (activeIndex + 1 === slideLength) {
                        next = self.children('li:first');
                    }
                }

                next.addClass('active');
                active.removeClass('active').addClass('prev');

                if (settings.animation === "fade") {
                    setTimeout(function () {
                        self.children('li.prev').removeClass('prev');
                    }, 500);
                }

                if (settings.animation === "slide") {
                    setTimeout(function () {
                        self.children('li.prev').removeClass('prev');
                    }, 250);
                }

                if (settings.nav === true) {
                    nextDot = navContainer.children('li').eq(next.index());
                    activeDot.removeClass('active');
                    nextDot.addClass('active');
                }

            });

            var interval;
/*
            function clearInterval(interval) {
                interval = setInterval(function () {
                    changeSlides(false);
                }, timer);
            }
*/
            function startChangeSlides() {
                interval = setInterval(function () {
                    changeSlides(true);
                }, timer);
            }
            startChangeSlides();

            if (settings.hover_pause === true) {
                self.hover(function () {
                    clearInterval(interval);
                }, function () {
                    startChangeSlides();
                });
            }

            if (settings.nav === true) {
                navContainer.children('li').on("click", function () {

                    clearInterval(interval);
                    navContainer.children('li').removeClass('active');

                    if (settings.animation === "fade") {
                        self.children('li.active').addClass('prev').removeClass('active');
                        setTimeout(function () {
                            self.children('li.prev').removeClass('prev');
                        }, 500);
                    }

                    if (settings.animation === "slide") {
                        self.children('li.active').addClass('prev').removeClass('active');
                        setTimeout(function () {
                            self.children('li.prev').removeClass('prev');
                        }, 250);
                    }

                    $(this).addClass('active');
                    var clickedDotIndex = $(this).index();
                    self.children('li').eq(clickedDotIndex).addClass('active');
                    startChangeSlides();
                });
            }

            if (settings.arrows === true) {
                self.parent('.blaster-slider').children('.prev-arrow').on('click', function (event) {
                    event.preventDefault();
                    clearInterval(interval);
                    changeSlides(false);
                    startChangeSlides();
                });

                self.parent('.blaster-slider').children('.next-arrow').on('click', function (event) {
                    event.preventDefault();
                    clearInterval(interval);
                    changeSlides(true);
                    startChangeSlides();
                });
            }
            
            if (typeof Hammer !== "undefined") {

                var hammertime = new Hammer(self[0]);

                hammertime.on('swipeleft', function () {
                    clearInterval(interval);
                    changeSlides(true);
                    startChangeSlides();
                });

                hammertime.on('swiperight', function () {
                    clearInterval(interval);
                    changeSlides(false);
                    startChangeSlides();
                });

            }

        });
    };
});
