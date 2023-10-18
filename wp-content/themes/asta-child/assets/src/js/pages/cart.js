
import '../../scss/views/cart.scss'
import { sendHttpReq } from '../utils/api'
import { wpr_alert } from '../utils/helpers'

const { nonce, json_url } = auctions_data
const chackout = document.querySelector('.chackout-btn')
const payment_form = document.querySelector('#payment-form')
const pay_submit = payment_form?.querySelector('#submit')


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
const on_submint = async (e, stripe, elements) => {

    e.preventDefault()

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
 * The function `build_checkout` creates a Stripe payment form and attaches it to a specified element
 * on the page, and then adds an event listener to submit the form.
 * @param intent_secret - The `intent_secret` parameter is a client secret for a Stripe PaymentIntent.
 * It is used to authenticate and authorize the payment transaction on the server-side.
 * @param pk - The `pk` parameter is the public key for your Stripe account. It is used to authenticate
 * your requests to the Stripe API and ensure that the client-side integration is secure. You can find
 * your public key in the Stripe Dashboard under the API Keys section.
 */
const build_chackout = async (intent_secret, pk) => {

    const stripe = Stripe(pk, { apiVersion: '2020-08-27' })
    const elements = stripe.elements({ clientSecret: intent_secret })

    const payment_element = elements.create('payment')
    payment_element.mount('#payment-element')

    payment_form.addEventListener('submit', e => on_submint(e, stripe, elements))
}


/**
 * The `chackout_process` function sends a HTTP request to a specified URL and handles the response by
 * displaying a payment form if the request is successful, or showing an error message if there is an
 * error.
 * @param e - The parameter "e" in the `checkout_process` function is typically used to represent the
 * event object that triggered the function. It is commonly used in event handlers to access
 * information about the event that occurred, such as the target element or the event type.
 */
const chackout_process = (e) => {

    sendHttpReq({
        url: json_url + 'api-cart-chackout',
        method: 'POST',
        headers: { 'X-WP-Nonce': nonce }
    }).then(async res => {

        res = JSON.parse(res)

        payment_form.classList.remove('d-none')
        chackout.classList.add('d-none')

        if ('error' !== res.status) {
            await build_chackout(res.client_secret, res.public_key)
        } else {
            wpr_alert([res.message])
        }

    }).catch(e => {
        console.log(e)
    })
}

chackout && chackout.addEventListener('click', ev => chackout_process(ev), false)
