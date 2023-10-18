import '../../scss/views/my_orders.scss'
import { sendHttpReq } from '../utils/api'
import { wpr_alert } from '../utils/helpers'

const { nonce, json_url } = auctions_data
const pay_now = document.querySelectorAll('.pay-now-order')


/**
 * The function `on_submit` is an asynchronous function that handles the submission of a payment form
 * using Stripe, disabling the submit button and displaying an error message if there is an error.
 * @param e - The parameter "e" is an event object that represents the event that triggered the
 * function. In this case, it is likely an event object related to a form submission.
 * @param stripe - The `stripe` parameter is an object that represents the Stripe API. It is used to
 * interact with the Stripe payment system and perform actions such as confirming payments.
 * @param pay_submit - The `pay_submit` parameter is a reference to the submit button element on the
 * form. It is used to disable the button while the payment is being processed to prevent multiple
 * submissions.
 */
const on_submint = async (e, stripe, pay_submit) => {

    e.preventDefault();

    pay_submit.disabled = true

    const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
            return_url: `${window.location.origin}/thank-you/`
        }
    })

    if (error) {
        wpr_alert([error.message])
        console.log(error.message)
        pay_submit.disabled = false
    }
}


/**
 * The function `build_checkout` is a JavaScript function that sets up a Stripe payment form with the
 * provided intent secret and public key, and handles the form submission event.
 * @param intent_secret - The `intent_secret` parameter is a string that represents the client secret
 * of the payment intent. This secret is used to authenticate the client and authorize the payment.
 * @param pk - The "pk" parameter is the public key for your Stripe account. It is used to authenticate
 * your client-side code and communicate securely with Stripe's servers.
 * @param payment_form - The `payment_form` parameter is the HTML form element that contains the
 * payment information. It is used to listen for the form submission event and handle the payment
 * processing.
 */
const build_chackout = async (intent_secret, pk, payment_form, order_id) => {

    const pay_submit = payment_form.querySelector('#submit')

    const stripe = Stripe(pk, { apiVersion: '2020-08-27' });
    const elements = stripe.elements({ clientSecret: intent_secret });

    const payment_element = elements.create('payment');
    payment_element.mount(`div[order_id="${order_id}"] .payment-element`);

    payment_form.addEventListener('submit', e => on_submint(e, stripe, pay_submit), false)
}


/**
 * The `chackout_process` function handles the checkout process by sending a HTTP request to a
 * specified URL, updating the payment form and button visibility based on the response, and building
 * the checkout form if there are no errors.
 * @param e - The parameter "e" is an event object that represents the event that triggered the
 * function. It is commonly used in event handlers to access information about the event, such as the
 * target element or the event type.
 * @param btn - The `btn` parameter is a reference to the button element that triggered the checkout
 * process.
 */
const chackout_process = (e, btn) => {

    const container = btn.closest('.item-container')
    const payment_form = container.querySelector('.payment-form')
    const order_id = container.getAttribute('order_id')

    sendHttpReq({
        url: json_url + 'api-pay-forgotten-order',
        method: 'POST',
        headers: { 'X-WP-Nonce': nonce },
        data: { order_id: order_id }
    }).then(async res => {

        res = JSON.parse(res)

        payment_form.classList.remove('d-none')
        btn.classList.add('d-none')

        if ('error' !== res.status) {
            await build_chackout(res.client_secret, res.public_key, payment_form, order_id)
        } else {
            wpr_alert([res.message])
        }

    }).catch(e => {
        console.log(e)
    })
}


pay_now.forEach(btn => btn.addEventListener('click', ev => chackout_process(ev, btn), false))