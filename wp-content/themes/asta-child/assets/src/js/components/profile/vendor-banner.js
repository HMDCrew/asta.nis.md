import { asta_modal, renderer } from '../../utils/asta-modal'

export class ProfileVendorBanner {


    constructor(profile_form, sendHttpReq, asta_alert, profile_data) {

        this.profile_form = profile_form
        this.asta_alert = asta_alert

        this.hash = new URL(window.location.href).hash
        this.masthead = document.querySelector('#masthead')
        this.banner = document.querySelector('.vendor-container')


        if (this.banner && this.profile_form) {

            this.image_profile = this.profile_form.querySelector('.user-picture .user-image img')
            this.user_name = this.profile_form.querySelector('input[name="full-name"]')

            this.init()

            this.subscribe = this.banner.querySelector('.ask-for-vendor')
            this.subscribe.addEventListener('click', ev => this.hundle_subscribe_vendor(), false)

        }
    }


    getOffset(el) {

        const rect = el.getBoundingClientRect()

        return {
            left: rect.left + window.scrollX,
            top: rect.top + window.scrollY
        }
    }


    init() {

        if (this.hash.includes('#seller')) {

            this.masthead.classList.add('scroll_down')

            window.scroll({
                behavior: 'smooth',
                left: 0,
                top: this.getOffset(this.banner).top - 100
            })
        }
    }


    validate_platform_requirements() {

        let errors = []

        if (this.image_profile.hasAttribute('placeholder')) {
            this.image_profile.classList.add('error')
            errors.push(this.image_profile)
        }

        if (1 >= this.user_name.value.trim().split(' ').length) {
            this.user_name.classList.add('error')
            errors.push(this.user_name)
        }

        return errors
    }


    modal() {

        const input = renderer('input', ['nome'], { type: 'text', placeholder: 'nome completo' })

        const close = renderer('button', ['close', 'btn', 'btn-primary'], { type: 'button' })
        close.textContent = 'Close'
        
        const send = renderer('button', ['send', 'btn', 'btn-primary'], { type: 'button' })
        send.textContent = 'Send'

        asta_modal(
            (content) => {
                content.append(input)
            },
            (actions) => {

                send.addEventListener('click', ev => { console.log('close') }, false)
                close.addEventListener('click', ev => { actions.closest('.asta-modal-overlay').remove() }, false)

                actions.append(close)
                actions.append(send)
            }
        )
    }

    hundle_subscribe_vendor() {

        const requirements = this.validate_platform_requirements()

        if (!requirements.length) {

            this.modal()

        } else {

            this.masthead.classList.add('scroll_down')

            window.scroll({
                behavior: 'smooth',
                left: 0,
                top: this.getOffset(requirements[0]).top - 100
            })

            this.asta_alert(['check required fields'])
        }
    }
}