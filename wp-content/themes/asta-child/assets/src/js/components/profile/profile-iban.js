import { Encryption } from '../Encryption'
import Cookies from 'js-cookie'

const iban = document.querySelector('input[name="iban"]')
const bic = document.querySelector('input[name="bic"]')

export class ProfileIban {

    constructor(profile_form, sendHttpReq, asta_alert, args) {

        this.profile_form = profile_form
        this.sendHttpReq = sendHttpReq
        this.asta_alert = asta_alert

        this.json_url = args.json_url
        this.nonce = args.nonce
        this.stripe_pk = args.stripe_pk

        if (this.profile_form && iban && bic) {

            iban.addEventListener('keyup', ev => this.hundel_iban(), false)
            iban.addEventListener('change', ev => this.hundel_iban(), false)
            iban.addEventListener('paste', ev => this.hundel_iban(), false)
            bic.addEventListener('keyup', ev => this.hundel_iban(), false)
            bic.addEventListener('change', ev => this.hundel_iban(), false)
            bic.addEventListener('paste', ev => this.hundel_iban(), false)

            iban.addEventListener('click', ev => this.get_iban(), false)
            bic.addEventListener('click', ev => this.get_iban(), false)
        }
    }

    save_iban(btn) {

        btn.classList.add('loading')

        this.sendHttpReq({
            url: this.json_url + 'api-get-asta-key',
            method: 'POST',
            headers: {
                'X-WP-Nonce': this.nonce
            }
        }).then(res => {

            res = JSON.parse(res)

            this.sendHttpReq({
                url: this.json_url + 'api-save-iban',
                data: {
                    key: res.key,
                    iv: res.iv,
                    text: new Encryption().encrypt(
                        JSON.stringify({ iban: iban.value, bic: bic.value }),
                        atob(Cookies.get('lommer_key'))
                    )
                },
                method: 'POST',
                headers: {
                    'X-WP-Nonce': this.nonce
                }
            }).then(res => {

                res = JSON.parse(res)

                btn.remove()
                this.asta_alert([res.message], 'success')

            }).catch(e => {
                console.log(e)
            })

        }).catch(e => {
            console.log(e)
        })
    }


    hundel_iban() {

        const container = iban.closest('.container-iban')

        if (!container.querySelector('.save-btn')) {

            const btn = document.createElement('button')
            btn.setAttribute('type', 'button')
            btn.classList.add('btn')
            btn.classList.add('btn-primary')
            btn.classList.add('save-btn')
            btn.textContent = 'save'

            btn.addEventListener('click', ev => this.save_iban(btn), false)

            container.append(btn)
        }

        iban.value = iban.value.toUpperCase().replace(/[^\dA-Z\•]/g, '').replace(/(.{4})/g, '$1 ').trim()
        bic.value = bic.value.toUpperCase()
    }


    get_iban() {

        const container = iban.closest('.container-iban')

        if (!container.classList.contains('decrypted')) {

            container.classList.add('decrypted')

            this.sendHttpReq({
                url: this.json_url + 'api-get-asta-key',
                method: 'POST',
                headers: {
                    'X-WP-Nonce': this.nonce
                }
            }).then(res => {

                res = JSON.parse(res)
                const key = Cookies.get('lommer_key')

                this.sendHttpReq({
                    url: this.json_url + 'api-get-iban',
                    data: {
                        key: res.key,
                        iv: res.iv
                    },
                    method: 'POST',
                    headers: {
                        'X-WP-Nonce': this.nonce
                    }
                }).then(res => {

                    res = JSON.parse(res)

                    const iban_res = JSON.parse(
                        new Encryption().decrypt(
                            res.message,
                            atob(key)
                        )
                    )

                    iban.value = iban_res.iban
                    bic.value = iban_res.bic

                }).catch(e => {
                    console.log(e)
                })

            }).catch(e => {
                console.log(e)
            })
        }
    }
}
