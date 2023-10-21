import EditorJS from '@editorjs/editorjs'
import Header from '@editorjs/header'
import List from '@editorjs/list'
import ImageTool from '@editorjs/image'
import Swiper from 'swiper';
import { Navigation, Manipulation, Thumbs } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import flatpickr from "flatpickr";
import 'flatpickr/dist/themes/dark.css'

import '../../scss/views/new_auction.scss'
import { sendHttpReq, sendHttpForm } from '../utils/api'
import { slide_image_url, move_empty_slide_last_position, append_slide } from '../utils/swiper'
import { simulate_click, wpr_alert } from '../utils/helpers'

const { json_url, nonce } = auction_data;

/**
 * This is a function that sends a simple POST request with optional data and returns a response or
 * error.
 * @param partial_url - The partial URL is a string that represents the endpoint of the API that the
 * HTTP POST request will be sent to. It is usually a part of the full URL and specifies the specific
 * resource or action that the request is targeting.
 * @param data - The `data` parameter is an optional parameter that represents the data to be sent in
 * the request body of the HTTP POST request. It can be an object, string, or any other valid data type
 * that can be sent in the request body. If no data is to be sent, the `data
 * @param response - The `response` parameter is a callback function that will be called when the HTTP
 * request is successful and a response is received. It takes one argument, which is the parsed JSON
 * response from the server.
 * @param error - The `error` parameter is a callback function that will be executed if there is an
 * error in the HTTP request. It will receive the error object as its argument.
 */
const simple_post_req = (partial_url, data, response, error) => {

    let args = {
        url: json_url + partial_url,
        method: 'POST',
        headers: {
            'X-WP-Nonce': nonce
        }
    }

    if (data !== null && typeof data === 'object' && Object.keys(data).length > 0) {
        args['data'] = data;
    }

    sendHttpReq(args).then(res => response(JSON.parse(res))).catch(e => error(e));
}


const init = (auction_id, auction_json) => {


    /**
     * The function binds actions to a new image slide in a gallery.
     * @param gallery - It is likely a reference to a gallery element in the HTML document, which will
     * be updated with new images when they are added.
     * @param thumbanils - It seems like there is a typo in the parameter name. It should be
     * "thumbnails" instead of "thumbanils".
     */
    const bind_new_image_actions = (gallery, thumbanils) => {

        const new_slide = document.querySelector('.thumbnails .new-content');
        const slide_image = new_slide && new_slide.querySelector('input[name="slide-image"]');

        if (new_slide && slide_image) {
            new_slide.addEventListener('click', (ev) => simulate_click(slide_image));
            slide_image.addEventListener('change', (ev) => add_gallery_image(slide_image, gallery, thumbanils), false)
        }
    }

    /**
     * The function removes a gallery image from an auction and updates the corresponding thumbnail and
     * gallery slides.
     * @param slide - The slide parameter is likely an object or identifier representing a specific image
     * slide in an auction gallery.
     */
    const remove_gallery_image = (slide) => {

        const image_url = slide_image_url(slide)

        simple_post_req(
            'api-auction-remove-image',
            {
                image_url,
                auction_id
            },
            (res) => {
                if ('success' === res.status) {

                    const old_thumbnail_slide = document.querySelector(`.thumbnails img[src="${image_url}"]`);
                    const old_gallery_slide = document.querySelector(`.auction-gallery img[src="${image_url}"]`);

                    old_thumbnail_slide.parentNode.remove();
                    old_gallery_slide.parentNode.remove();

                } else {
                    wpr_alert([res.message])
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
    const remove_slide_event = (remove_slides) => {
        remove_slides.forEach(async btn => {
            const slide = btn.parentNode
            slide.classList.remove('widouth-events')
            btn.addEventListener('click', (ev) => remove_gallery_image(btn.parentNode), false)
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
    const add_gallery_image = (image, gallery, thumbanils) => {

        if (image.files.length > 0) {

            let formData = new FormData();
            formData.append("file", image.files[0]);
            formData.append("auction_id", auction_id);

            sendHttpForm({
                url: json_url + 'api-auction-upload-image',
                data: formData,
                headers: { 'X-WP-Nonce': nonce }
            }).then(res => {

                res = JSON.parse(res)

                if ('success' === res.status) {

                    const remove_slide = '<button type="button" class="remove-slide"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px" height="24px"><path d="M 10.806641 2 C 10.289641 2 9.7956875 2.2043125 9.4296875 2.5703125 L 9 3 L 4 3 A 1.0001 1.0001 0 1 0 4 5 L 20 5 A 1.0001 1.0001 0 1 0 20 3 L 15 3 L 14.570312 2.5703125 C 14.205312 2.2043125 13.710359 2 13.193359 2 L 10.806641 2 z M 4.3652344 7 L 5.8925781 20.263672 C 6.0245781 21.253672 6.877 22 7.875 22 L 16.123047 22 C 17.121047 22 17.974422 21.254859 18.107422 20.255859 L 19.634766 7 L 4.3652344 7 z"></path></svg></button>'
                    append_slide(`<img src="${res.url}" />${remove_slide}`, gallery, thumbanils);

                    const new_slides = document.querySelectorAll('.swiper-slide.widouth-events .remove-slide')
                    remove_slide_event(new_slides);

                    // move to last position empty slide
                    move_empty_slide_last_position(gallery)
                    move_empty_slide_last_position(thumbanils)

                    bind_new_image_actions(gallery, thumbanils)
                } else {
                    console.log(res)
                }

            }).catch(e => {
                console.log(e);
            });
        }
    }


    const swiper = new Swiper(".thumbnails", {
        loop: true,
        modules: [Manipulation],
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
        on: {
            init: (swiper) => remove_slide_event(
                swiper.el.querySelectorAll('.remove-slide')
            ),
        },
    });


    const swiper2 = new Swiper(".auction-gallery", {
        loop: true,
        modules: [Navigation, Thumbs, Manipulation],
        spaceBetween: 10,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        thumbs: {
            swiper: swiper,
        },
        observer: true,
        observeParents: true,
        on: {
            init: (swiper) => remove_slide_event(
                swiper.el.querySelectorAll('.remove-slide')
            ),
        },
    });


    bind_new_image_actions(swiper2, swiper);


    flatpickr("#litepicker", {
        mode: "range",
        minDate: "today",
        dateFormat: "d/m/Y",
    });


    const editor = new EditorJS({
        /** 
         * Id of Element that should contain the Editor 
         */
        holder: 'editorjs',

        data: { blocks: auction_json },

        placeholder: 'Click here to add a full description',

        /** 
         * Available Tools list. 
         * Pass Tool's class or Settings object for each Tool you want to use 
         */
        tools: {
            header: {
                class: Header,
                inlineToolbar: ['link']
            },
            list: {
                class: List,
                inlineToolbar: true,
                config: {
                    defaultStyle: 'unordered'
                }
            },
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: json_url + 'api-auction-upload-image',
                    }
                }
            },
        },
    })

    const save_auction = document.querySelector('.sidebar .save')

    const auction_title = document.querySelector('input[name="asta-title"]')
    const auction_date = document.querySelector('input[name="auction-date"]')
    const baze_price = document.querySelector('input[name="price"]')
    const price_increment = document.querySelector('input[name="price-increment"]')
    const auction_type_select = document.querySelector('.wrap-input.select select[name="category"]')
    const aditional_info = document.querySelector('textarea[name="aditional-info"]')

    const salve_auction_info = () => {

        const type_select = auction_type_select.parentNode.querySelector('input.select');

        editor.save().then((editor_data) => {

            const data = {
                auction_id: auction_id,
                auction_title: auction_title.value,
                auction_date: auction_date.value,
                baze_price: baze_price.value,
                price_increment: price_increment.value,
                auction_type_select: type_select.value,
                auction_type_select_id: type_select.getAttribute('content'),
                aditional_info: aditional_info.value,
                auction_content: editor_data.blocks
            };

            simple_post_req(
                'api-save-auction',
                data,
                (res) => {
                    if ('success' === res.status) {
                        wpr_alert([res.message], 'success')

                        if (!document.body.classList.contains('page-template-edit-auction')) {
                            window.location.replace(`/edit-auction/?auction_id=${auction_id}`)
                        }
                    } else {
                        wpr_alert([res.message])
                    }
                },
                (e) => console.log(e)
            );

        }).catch((error) => {
            console.log('Saving failed: ', error)
        });
    }

    if (save_auction && auction_date && baze_price && price_increment && auction_type_select && aditional_info) {
        save_auction.addEventListener('click', ev => salve_auction_info(), false)
    }
}

const is_edit_auction = document.body.classList.contains('page-template-edit-auction');

simple_post_req(
    is_edit_auction ? 'api-edit-auction' : 'api-new-auction',
    is_edit_auction ? { auction_id: new URLSearchParams(window.location.search).get('auction_id') } : {},
    (res) => {
        if ('success' === res.status) {
            init(res.auction_id, res.auction_json)
        } else {
            wpr_alert([res.message])
        }
    },
    (e) => console.log(e)
);
