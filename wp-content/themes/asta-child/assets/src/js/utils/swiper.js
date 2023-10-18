/**
 * This function moves the empty slide to the last position in a Swiper slider.
 * @param slider - The `slider` parameter is an object that represents a Swiper instance. It
 * contains various properties and methods that allow for the manipulation and control of the
 * slider.
 */
export const move_empty_slide_last_position = (slider) => {

    const slide = slider.el.querySelector('.empty-slide');
    const slide_id = slide.getAttribute('data-swiper-slide-index')

    const clone = slide.cloneNode(true)
    clone.removeAttribute('style')
    clone.removeAttribute('data-swiper-slide-index')

    slider.removeSlide(slide_id)
    slider.appendSlide(clone);
}


/**
 * The function appends a slide to a gallery and its thumbnails using Swiper.js.
 * @param slide - The HTML content of the slide that needs to be appended to the gallery and
 * thumbnails.
 * @param gallery - It is likely that `gallery` is an instance of a Swiper.js gallery, which is a
 * popular JavaScript library for creating responsive and touch-enabled sliders. The `appendSlide`
 * method is a built-in method of Swiper.js that adds a new slide to the end of the gallery.
 * @param thumbanils - It seems like there is a typo in the parameter name. It should be "thumbnails"
 * instead of "thumbanils".
 */
export const append_slide = (slide, gallery, thumbanils) => {

    const div = document.createElement('div')
    div.classList.add('swiper-slide')
    div.classList.add('widouth-events')
    div.innerHTML = slide

    gallery.appendSlide(div.outerHTML);
    thumbanils.appendSlide(div.outerHTML);
}


/**
 * The function returns the URL of an image within a given container element.
 * @param img_container - The `img_container` parameter is expected to be a DOM element that contains
 * an `img` element. The function will then retrieve the `src` attribute of the `img` element and
 * return it as a string. If the `img` element is not found, an empty string will be returned
 * @returns The function `slide_image_url` returns the value of the `src` attribute of the `img`
 * element inside the `img_container` parameter, or an empty string if there is no `img` element or it
 * has no `src` attribute.
 */
export const slide_image_url = (img_container) => {
    const img = img_container.querySelector('img')
    return img ? img.getAttribute('src') : ''
}