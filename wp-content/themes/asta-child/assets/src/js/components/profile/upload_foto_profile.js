import { sendHttpForm } from '../../utils/api/form'

export class ProfilePicture {

    constructor(profile_form, args) {

        this.json_url = args.json_url
        this.nonce = args.nonce

        if (profile_form) {

            this.photo_profile = profile_form.querySelector('input[type="file"]')
            this.photo_profile.addEventListener('change', (ev) => this.upload_picture(), false)
        }
    }

    /**
     * The function `upload_picture()` uploads a selected picture file to a server using an HTTP
     * request and updates the image source if the upload is successful.
     */
    upload_picture() {

        if (this.photo_profile.files.length > 0) {

            const container = this.photo_profile.parentNode

            // loading
            container.classList.add('loading')

            let formData = new FormData()
            formData.append("file", this.photo_profile.files[0])

            sendHttpForm({
                url: this.json_url + 'api-profile-upload-image',
                data: formData,
                headers: {
                    'X-WP-Nonce': this.nonce
                }
            }).then(res => {

                res = JSON.parse(res)

                if ('success' === res.status) {

                    const img = container.querySelector('img')

                    img.setAttribute('src', res.image)
                    img.removeAttribute('placeholder')
                    img.classList.remove('error')

                } else {
                    console.log(res)
                }

                // end loading 
                container.classList.remove('loading')

            }).catch(e => {
                console.log(e)
                // end loading
                container.classList.remove('loading')
            })
        }
    }
}
