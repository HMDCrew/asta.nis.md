
export class ProfilePaymentCards {

    constructor(profile_form, sendHttpReq, asta_alert, args) {

        this.profile_form = profile_form
        this.sendHttpReq = sendHttpReq
        this.asta_alert = asta_alert

        this.json_url = args.json_url
        this.nonce = args.nonce
        this.stripe_pk = args.stripe_pk

        if (this.profile_form) {

            this.cards_form = this.profile_form.querySelector('#cards-form')
            this.add_card = this.cards_form?.querySelector('#submit')
            this.carte_errors = this.cards_form.querySelector('#card-errors')
            this.contaier_carte = this.profile_form.querySelector('.contaier-carte')

            const carte_card = this.profile_form.querySelector('.create-cart')
            carte_card && carte_card.addEventListener('click', ev => this.build_card_form(this.stripe_pk), false)
        }
    }


    /**
     * The function `render_new_card` creates and returns a new card element with the brand and last
     * four digits of a payment card.
     * @param payment_card - The `payment_card` parameter is an object that represents a payment card.
     * It should have the following properties:
     * @returns a newly created card element with the payment card information.
     */
    render_new_card(payment_card) {

        const card = document.createElement('div')
        card.classList.add('card')

        card.innerHTML = (
            `<div class="card-type">${payment_card.brand}</div><div class="card-numbers">**** **** **** ${payment_card.last4}</div>`
        )

        return card
    }


    /**
     * The function `hundle_add_card` creates a token using the Stripe API and sends a POST request to
     * add a new card to a user's account.
     * @param stripe - The "stripe" parameter is an object that represents the Stripe API. It is used
     * to interact with the Stripe payment platform and perform operations such as creating tokens for
     * card payments.
     * @param card_element - The `card_element` parameter is an element that represents the card
     * information entered by the user. It is used by the `stripe.createToken()` method to create a
     * token for the card.
     */
    hundle_add_card(stripe, card_element) {

        stripe.createToken(card_element).then((result) => {

            if (result.error) {
                this.carte_errors.textContent = result.error.message;
            } else {
                this.sendHttpReq({
                    url: this.json_url + 'api-card-to-user',
                    method: 'POST',
                    data: {
                        token: result.token.id
                    },
                    headers: { 'X-WP-Nonce': this.nonce }
                }).then(async res => {

                    res = JSON.parse(res)

                    this.contaier_carte.append(this.render_new_card(res.message))

                    this.cards_form.parentNode.classList.add('d-none')

                }).catch(e => {
                    this.asta_alert([e])
                    console.log(e)
                })
            }
        })
    }


    /**
     * The function builds a card form using Stripe's API and adds an event listener to handle adding a
     * new card.
     * @param pk - The "pk" parameter is the public key for your Stripe account. It is used to
     * authenticate your requests to the Stripe API and ensure that only authorized users can access
     * and modify your account information.
     */
    async build_card_form(pk) {

        const stripe = Stripe(pk, { apiVersion: '2020-08-27' })

        const elements = stripe.elements()
        const card_element = elements.create('card')
        card_element.mount('#card-element')

        this.cards_form.parentNode.classList.remove('d-none')

        this.add_card.addEventListener('click', ev => this.hundle_add_card(stripe, card_element), false)
    }
}
