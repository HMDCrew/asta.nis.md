import Swiper from 'swiper'
import { Manipulation, Thumbs } from 'swiper/modules'

import { sendHttpForm } from '../../../utils/api/form'
import { slide_image_url, move_empty_slide_last_position, append_slide } from '../../../utils/swiper'
import { asta_alert } from '../../../utils/asta_alert'
import { simulate_click } from '../../../utils/simulate_click'

export class Gallery {

    constructor(product_id, simple_post_req, args) {

        this.product_id = product_id
        this.simple_post_req = simple_post_req

        this.json_url = args.json_url
        this.nonce = args.nonce

        this.setup_gallery()
    }


    /**
     * The function removes a gallery image from an auction and updates the corresponding thumbnail and
     * gallery slides.
     * @param slide - The slide parameter is likely an object or identifier representing a specific image
     * slide in an auction gallery.
     */
    remove_gallery_image(slide) {

        const image_url = slide_image_url(slide)

        this.simple_post_req(
            'api-product-remove-image',
            {
                image_url,
                product_id: this.product_id
            },
            (res) => {
                if ('success' === res.status) {

                    const old_thumbnail_slide = document.querySelector(`.thumbnails img[src="${image_url}"]`);
                    const old_gallery_slide = document.querySelector(`.gallery img[src="${image_url}"]`);

                    old_thumbnail_slide.parentNode.remove();
                    old_gallery_slide.parentNode.remove();

                } else {
                    asta_alert([res.message])
                }
            },
            (e) => console.log(e)
        );
    }


    /**
     * This function removes the "widouth-events" class from a slide and adds a click event listener to
     * a button within the slide to remove the gallery image.
     * @param remove_slides - `remove_slides` is an array of buttons that are used to remove slides
     * from a gallery. The function loops through each button in the array and adds a click event
     * listener to it. When the button is clicked, it calls the `remove_gallery_image` function with
     * the parent node of the button
     */
    remove_slide_event(remove_slides) {
        remove_slides.forEach(async btn => {
            const slide = btn.parentNode
            slide.classList.remove('widouth-events')
            btn.addEventListener('click', (ev) => this.remove_gallery_image(btn.parentNode), false)
        });
    }


    /**
     * The function adds an image to a gallery and thumbnails, and moves the last empty slide to the end.
     * @param image - an object representing the file input element that contains the image to be uploaded
     * @param gallery - The gallery parameter is likely a reference to a DOM element that represents the
     * main gallery container where the uploaded image will be displayed.
     * @param thumbanils - It seems like a typo in the code, the correct parameter name should be
     * "thumbnails" instead of "thumbanils". It is likely an object representing the thumbnails gallery
     * where the uploaded image will be displayed as a thumbnail.
     */
    add_gallery_image(image, gallery, thumbanils) {

        if (image.files.length > 0) {

            let formData = new FormData();
            formData.append("file", image.files[0]);
            formData.append("product_id", product_id);

            sendHttpForm({
                url: this.json_url + 'api-product-upload-image',
                data: formData,
                headers: { 'X-WP-Nonce': this.nonce }
            }).then(res => {

                res = JSON.parse(res)

                if ('success' === res.status) {

                    const remove_slide = '<button type="button" class="remove-slide"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px" height="24px"><path d="M 10.806641 2 C 10.289641 2 9.7956875 2.2043125 9.4296875 2.5703125 L 9 3 L 4 3 A 1.0001 1.0001 0 1 0 4 5 L 20 5 A 1.0001 1.0001 0 1 0 20 3 L 15 3 L 14.570312 2.5703125 C 14.205312 2.2043125 13.710359 2 13.193359 2 L 10.806641 2 z M 4.3652344 7 L 5.8925781 20.263672 C 6.0245781 21.253672 6.877 22 7.875 22 L 16.123047 22 C 17.121047 22 17.974422 21.254859 18.107422 20.255859 L 19.634766 7 L 4.3652344 7 z"></path></svg></button>'
                    append_slide(`<img src="${res.url}" />${remove_slide}`, gallery, thumbanils);

                    const new_slides = document.querySelectorAll('.swiper-slide.widouth-events .remove-slide')
                    this.remove_slide_event(new_slides);

                    // move to last position empty slide
                    move_empty_slide_last_position(gallery)
                    move_empty_slide_last_position(thumbanils)

                    this.bind_new_image_actions(gallery, thumbanils)
                } else {
                    console.log(res)
                }

            }).catch(e => {
                console.log(e);
            });
        }
    }


    /**
     * The function binds actions to a new image slide in a gallery.
     * @param gallery - It is likely a reference to a gallery element in the HTML document, which will
     * be updated with new images when they are added.
     * @param thumbanils - It seems like there is a typo in the parameter name. It should be
     * "thumbnails" instead of "thumbanils".
     */
    bind_new_image_products(gallery, thumbanils) {

        const new_slide = document.querySelector('.thumbnails .new-content');
        const slide_image = new_slide && new_slide.querySelector('input[name="slide-image"]');

        if (new_slide && slide_image) {
            new_slide.addEventListener('click', (ev) => simulate_click(slide_image));
            slide_image.addEventListener('change', (ev) => this.add_gallery_image(slide_image, gallery, thumbanils), false)
        }
    }


    setup_thumbnails() {
        return new Swiper(".thumbnails", {
            loop: true,
            modules: [Manipulation],
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
            on: {
                init: (swiper) => this.remove_slide_event(
                    swiper.el.querySelectorAll('.remove-slide')
                ),
            },
        })
    }


    setup_gallery() {

        const next = document.querySelector('.gallery .swiper-button-next')
        const prev = document.querySelector('.gallery .swiper-button-prev')

        const swiper = this.setup_thumbnails()

        const swiper2 = new Swiper(".gallery", {
            loop: true,
            modules: [Thumbs, Manipulation],
            spaceBetween: 10,
            thumbs: {
                swiper: swiper,
            },
            observer: true,
            observeParents: true,
            on: {
                init: (swiper) => {

                    this.remove_slide_event(swiper.el.querySelectorAll('.remove-slide'))

                    if (swiper.slides.length > 0) {
                        next.addEventListener('click', ev => swiper.slideNext(), false)
                        prev.addEventListener('click', ev => swiper.slidePrev(), false)
                    } else {
                        prev.classList.add('swiper-button-disabled')
                        next.classList.add('swiper-button-disabled')
                    }
                },
            },
        })

        this.bind_new_image_products(swiper2, swiper);
    }
}