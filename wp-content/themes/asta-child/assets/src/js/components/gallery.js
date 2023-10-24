import { Fancybox } from '@fancyapps/ui'
import Swiper from 'swiper'
import { Thumbs } from 'swiper/modules'

export class Gallery {

    constructor() {
        this.setup_gallery()
    }

    /**
     * The function sets up a Swiper instance for a thumbnails container with specific configuration
     * options.
     * @param Swiper - The Swiper parameter is the class or selector of the container element that
     * contains the thumbnail images. It is used to initialize the Swiper instance on that container
     * element.
     * @returns a new instance of the Swiper class, which is initialized with the specified options.
     */
    setup_thumbnails() {
        return new Swiper(".thumbnails", {
            loop: true,
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });
    }

    /**
     * The function sets up a gallery using the Swiper library, with Fancybox for image viewing and
     * Thumbs for thumbnail navigation.
     * @param Swiper - Swiper is a JavaScript library that allows you to create touch-enabled sliders
     * and carousels. It provides a set of options and methods to customize and control the behavior of
     * the slider.
     * @param Fancybox - Fancybox is a JavaScript library that provides a lightbox functionality for
     * displaying images, videos, and other media in a modal window. It allows users to view the media
     * in a larger size and provides options for navigation and interaction.
     * @param Thumbs - Thumbs is a module that is used in conjunction with Swiper to create a thumbnail
     * gallery. It allows you to display a smaller version of each slide as a thumbnail, which can be
     * clicked to navigate to the corresponding slide in the main gallery.
     */
    setup_gallery() {

        const next = document.querySelector('.gallery .swiper-button-next')
        const prev = document.querySelector('.gallery .swiper-button-prev')

        new Swiper(".gallery", {
            loop: true,
            modules: [Thumbs],
            spaceBetween: 10,
            on: {
                init: (swipper) => {

                    Fancybox.bind('[data-fancybox="gallery"]')

                    if (swipper.slides.length > 0) {
                        next.addEventListener('click', ev => swipper.slideNext(), false)
                        prev.addEventListener('click', ev => swipper.slidePrev(), false)
                    } else {
                        prev.classList.add('swiper-button-disabled')
                        next.classList.add('swiper-button-disabled')
                    }
                },
            },
            thumbs: {
                swiper: this.setup_thumbnails(),
            },
        });
    }
}