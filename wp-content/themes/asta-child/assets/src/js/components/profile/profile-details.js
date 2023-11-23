import { check_pwd_validity } from '../../utils/passwords'

export class ProfileDetails {

    constructor(profile_form, sendHttpReq, asta_alert, args) {

        this.profile_form = profile_form
        this.sendHttpReq = sendHttpReq
        this.asta_alert = asta_alert

        this.json_url = args.json_url
        this.nonce = args.nonce

        if (this.profile_form) {

            this.btn_update_profile = this.profile_form.querySelector('.update-profile')
            this.pwd = this.profile_form.querySelector('input[name="password"]')
            this.repeat_pwd = this.profile_form.querySelector('input[name="repeat-password"]')

            this.pwd.addEventListener('keyup', (ev) => this.check_pwd(), false)
            this.repeat_pwd.addEventListener('keyup', (ev) => this.check_pwd(), false)
            this.btn_update_profile.addEventListener('click', (ev) => this.update_profile_info(ev), false)
        }
    }

    /**
     * The function checks the validity of two password inputs and adds appropriate CSS classes based
     * on their match.
     */
    check_pwd() {

        if (
            'password' !== this.repeat_pwd.value && 'password' !== this.pwd.value &&
            check_pwd_validity(this.pwd.value, this.repeat_pwd.value)
        ) {
            this.pwd.classList.remove('error')
            this.repeat_pwd.classList.remove('error')
            this.pwd.classList.add('success')
            this.repeat_pwd.classList.add('success')
        } else {
            this.pwd.classList.remove('success')
            this.repeat_pwd.classList.remove('success')
            this.pwd.classList.add('error')
            this.repeat_pwd.classList.add('error')
        }
    }


    /**
     * The function `update_profile_info` is used to update the user's profile information by sending a
     * HTTP request to the server.
     * @param e - The parameter "e" is an event object that is passed to the function. It is typically
     * used to prevent the default behavior of an event, such as form submission, by calling the
     * `preventDefault()` method on it.
     */
    update_profile_info (e) {

        e.preventDefault()
    
        const full_name = this.profile_form.querySelector('input[name="full-name"]')
        const email = this.profile_form.querySelector('input[name="email"]')
        const password = this.profile_form.querySelector('input[name="password"]')
        const repeat_password = this.profile_form.querySelector('input[name="repeat-password"]')
    
        let data = {
            full_name: full_name.value,
            email: email.value,
        }
    
        if (
            'password' !== this.repeat_pwd.value && 'password' !== this.pwd.value &&
            check_pwd_validity(password.value, repeat_password.value)
        ) {
            data['password'] = password.value
            data['repeat_password'] = repeat_password.value
        }
    
        this.sendHttpReq({
            url: this.json_url + 'api-profile-update-info',
            data: data,
            method: 'POST',
            headers: {
                'X-WP-Nonce': this.nonce
            }
        }).then(res => {
    
            res = JSON.parse(res)
    
            if ('success' === res.status) {
                this.asta_alert([res.message], 'success')
            } else {
                this.asta_alert([res.message])
            }
    
        }).catch(e => {
            console.log(e)
        })
    }
}