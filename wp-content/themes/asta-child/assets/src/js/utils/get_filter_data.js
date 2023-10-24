const filter_bar = document.querySelector('.filter-bar')


/**
 * The function "get_price_object" returns an array containing the values of "value1" and "value2"
 * from a given DOM element, or an array of two "false" values if either of the values is empty.
 * @param dom_element - The `dom_element` parameter is a reference to a DOM element object. It is
 * expected to have two properties: `value1` and `value2`.
 * @returns an array containing the values of `dom_element.value1` and `dom_element.value2`. If
 * both values are not empty strings, the array will contain the values. Otherwise, the array will
 * contain `false` for both values.
 */
const get_price_object = (dom_element) => {

    if ('' !== dom_element.value1.toString() && '' !== dom_element.value2.toString()) {
        return [dom_element.value1, dom_element.value2]
    }

    return [false, false]
}


/**
 * The function "get_date_object" takes a string value and returns an array with two elements,
 * representing the start and end dates of a range if the value is not empty, otherwise it returns
 * an array with two false values.
 * @param value - The `value` parameter is a string that represents a date range. It can be in the
 * format "start_date to end_date".
 * @returns an array with two elements. The first element is either the start date or false if the
 * value is an empty string. The second element is either the end date or false if the value is an
 * empty string.
 */
const get_date_object = (value) => {

    if ('' !== value.trim()) {
        const range = value.split(' to ')
        return [range[0], range[1]]
    }

    return [false, false]
}


/**
 * The function `get_filter_data` retrieves filter data from various elements on the page and
 * returns it as an object.
 * @param [page=1] - The page parameter is used to specify the page number for pagination. It
 * determines which page of data to retrieve.
 * @returns the `data` object.
 */
export const get_filter_data = (page = 1, user_id = null) => {

    let args = {}
    let data = {}

    const search = filter_bar.querySelector('.search input[name="search"]')
    if (search) {
        args['search'] = search.value
    }

    const type = filter_bar.querySelector('.search .wrap-input input.select')
    const category = type && type.getAttribute('content')
    if (type) {
        args['type'] = category
    }

    const date_rage = filter_bar.querySelector('input[name="date-range"]')
    if (date_rage) {
        args['date_rage'] = get_date_object(date_rage.value);
    }

    const price_range = filter_bar.querySelector('#price_range')
    if (price_range) {
        args['price_range'] = get_price_object(price_range)
    }

    if (
        (search && type && date_rage && price_range) || // => Auction archive
        (search && type && price_range) // => Shop archive
    ) {
        args['page'] = page
        data = args
    } else {
        data = { page: page }
    }

    if (
        document.body.classList.contains('page-template-my-auctions') ||
        document.body.classList.contains('page-template-my-products')
    ) {
        data['user_id'] = user_id
    }

    return data;
}