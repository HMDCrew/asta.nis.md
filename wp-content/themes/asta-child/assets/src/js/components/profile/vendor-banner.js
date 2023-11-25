import { asta_modal, renderer } from '../../utils/asta-modal'

const vendor_form_content = (
	`<div class="row">
        <div class="col-6">
            <div class="wrap-input">
                <input class="input full-name" type="text" name="full-name" placeholder="Full Name" value="">
                <span class="focus-input"></span>
                <span class="symbol-input">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"></path></svg>
                </span>
            </div>
        </div>
        <div class="col-6">
            <div class="wrap-input">
                <input type="text" name="residence" class="input residence" placeholder="Residence address" value="" required />
                <span class="focus-input"></span>
                <span class="symbol-input">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="20 19 60 60"><path d="M49,18.92A23.74,23.74,0,0,0,25.27,42.77c0,16.48,17,31.59,22.23,35.59a2.45,2.45,0,0,0,3.12,0c5.24-4.12,22.1-19.11,22.1-35.59A23.74,23.74,0,0,0,49,18.92Zm0,33.71a10,10,0,1,1,10-10A10,10,0,0,1,49,52.63Z"/></svg>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="wrap-input">
                <input type="date" name="birth-day" class="input birth-day" placeholder="Date of birth" value="" required />
                <span class="focus-input"></span>
                <span class="symbol-input">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="2 2 20 20"><path d="M6,22H18a3,3,0,0,0,3-3V7a2,2,0,0,0-2-2H17V3a1,1,0,0,0-2,0V5H9V3A1,1,0,0,0,7,3V5H5A2,2,0,0,0,3,7V19A3,3,0,0,0,6,22ZM5,12.5a.5.5,0,0,1,.5-.5h13a.5.5,0,0,1,.5.5V19a1,1,0,0,1-1,1H6a1,1,0,0,1-1-1Z"/></svg>
                </span>
            </div>
        </div>
        <div class="col-6">
            <div class="wrap-input">
                <input type="text" name="VAT" class="input vat" placeholder="VAT/IVA" value="" required />
                <span class="focus-input"></span>
                <span class="symbol-input">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000" version="1.1" id="Capa_1" width="800px" height="800px" viewBox="0 0 508.813 508.813" xml:space="preserve"><path d="M479.073,409.127H29.741c-5.372,0-9.727,4.355-9.727,9.728v80.231c0,5.371,4.354,9.728,9.727,9.728h449.332    c5.373,0,9.727-4.355,9.727-9.728v-80.231C488.799,413.482,484.446,409.127,479.073,409.127z M301.407,454.406h-94    c-8.284,0-15-6.716-15-15s6.716-15,15-15h94c8.285,0,15,6.716,15,15S309.692,454.406,301.407,454.406z"/><path d="M38.407,398.209h432c8.285,0,15-6.717,15-15l-20-282c0-8.285-6.715-15-15-15h-87.076V68.81h47.154    c5.045,0,9.135-4.088,9.135-9.133v-5.48V14.615v-5.48c0-5.045-4.09-9.135-9.135-9.135H265.519c-5.044,0-9.135,4.09-9.135,9.135    v5.48v45.063c0,5.045,4.091,9.133,9.135,9.133h47.153v17.397H58.407c-8.284,0-15,6.715-15,15l-20,282    C23.407,391.492,30.123,398.209,38.407,398.209z M318.629,245.153c-0.631,7.896-0.945,11.843-1.578,19.735    c-0.334,4.16-3.752,7.535-7.633,7.535c-8.155,0-16.311,0-24.465,0.002c-3.881,0-6.465-3.377-5.773-7.537    c1.312-7.894,1.967-11.84,3.277-19.735c0.691-4.161,4.154-7.536,7.732-7.536c7.52,0,15.041,0,22.561,0    C316.331,237.617,318.961,240.992,318.629,245.153z M367.167,371.875c-9.969,0.002-19.938,0.002-29.906,0    c-4.742,0-8.443-3.371-8.264-7.533c0.34-7.895,0.51-11.846,0.85-19.74c0.18-4.159,3.926-7.532,8.367-7.532    c9.334,0,18.668,0,28.002,0c4.441,0,8.188,3.373,8.367,7.532c0.34,7.896,0.51,11.847,0.85,19.74    C375.612,368.504,371.911,371.875,367.167,371.875z M339.573,287.342c8.428,0,16.854,0,25.281,0c4.012,0,7.404,3.375,7.586,7.535    c0.34,7.896,0.51,11.842,0.85,19.737c0.178,4.161-3.17,7.534-7.482,7.534c-9.062,0-18.125,0-27.188,0    c-4.312,0-7.662-3.373-7.48-7.534c0.34-7.896,0.51-11.843,0.85-19.737C332.168,290.717,335.563,287.342,339.573,287.342z     M333.28,264.889c0.34-7.894,0.51-11.84,0.85-19.735c0.18-4.161,3.227-7.536,6.805-7.536c7.521,0,15.041,0,22.562,0    c3.578,0,6.625,3.375,6.803,7.536c0.342,7.896,0.512,11.843,0.852,19.735c0.18,4.16-2.822,7.535-6.703,7.535    c-8.154,0-16.309,0-24.465,0C336.102,272.426,333.1,269.051,333.28,264.889z M385.799,245.153    c-0.332-4.161,2.299-7.536,5.877-7.536c7.521,0,15.043,0,22.564,0c3.578,0,7.039,3.375,7.73,7.536    c1.311,7.896,1.967,11.843,3.277,19.735c0.691,4.16-1.895,7.535-5.773,7.535c-8.156,0-16.312,0-24.469,0    c-3.881,0-7.297-3.375-7.629-7.535C386.747,256.996,386.43,253.049,385.799,245.153z M438.493,344.602    c1.312,7.895,1.967,11.846,3.279,19.74c0.689,4.16-2.594,7.532-7.338,7.532c-9.971,0-19.939,0-29.908,0    c-4.744,0-8.861-3.372-9.193-7.532c-0.631-7.896-0.947-11.847-1.578-19.74c-0.332-4.16,2.998-7.533,7.439-7.533    c9.334,0.001,18.67,0.001,28.004,0C433.639,337.068,437.801,340.441,438.493,344.602z M426.954,322.148    c-9.062,0-18.125,0-27.188,0c-4.314,0-8.08-3.373-8.412-7.534c-0.631-7.896-0.945-11.843-1.578-19.737    c-0.332-4.16,2.646-7.535,6.656-7.535c8.43,0,16.855,0,25.285,0c4.01,0,7.82,3.375,8.514,7.535    c1.311,7.896,1.967,11.842,3.277,19.737C434.2,318.775,431.264,322.148,426.954,322.148z M272.241,114.875c0-2.762,2.238-5,5-5    h148c2.762,0,5,2.238,5,5v92c0,2.762-2.238,5-5,5h-148c-2.762,0-5-2.238-5-5V114.875z M270.917,314.614    c1.312-7.896,1.971-11.843,3.281-19.737c0.691-4.16,4.504-7.535,8.514-7.535c8.428,0,16.855,0,25.281,0    c4.012,0,6.99,3.375,6.658,7.535c-0.631,7.896-0.947,11.842-1.578,19.737c-0.334,4.161-4.1,7.534-8.41,7.534    c-9.063,0-18.125,0-27.188,0.001C273.163,322.149,270.226,318.775,270.917,314.614z M262.657,364.342    c1.312-7.895,1.969-11.846,3.279-19.74c0.691-4.159,4.854-7.532,9.295-7.532c9.336,0.001,18.668,0,28.004,0    c4.439,0,7.77,3.373,7.438,7.532c-0.631,7.896-0.945,11.847-1.578,19.74c-0.334,4.162-4.448,7.533-9.191,7.533    c-9.97,0-19.938,0-29.908,0C265.252,371.875,261.967,368.504,262.657,364.342z M155.241,114.875c0-2.762,2.238-5,5-5h50    c2.762,0,5,2.238,5,5v92c0,2.762-2.238,5-5,5h-50c-2.762,0-5-2.238-5-5V114.875z M78.573,114.875c0-2.762,2.238-5,5-5h50    c2.762,0,5,2.238,5,5v92c0,2.762-2.238,5-5,5h-50c-2.762,0-5-2.238-5-5V114.875z"/></svg>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="wrap-input">
                <input type="text" name="company" class="input company" placeholder="Company name" value="" required />
                <span class="focus-input"></span>
                <span class="symbol-input">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" viewBox="0 0 48 48" version="1.1" id="svg8" xml:space="preserve"><defs id="defs4"><style id="style2">.a{fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;}</style><style id="style822">.cls-1{fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;}</style><style id="style913">.cls-1{fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;}</style></defs><path id="rect650" style="opacity:1;vector-effect:none;fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:1;stroke-linecap:round;stroke-linejoin:round;stop-color:#000000;stop-opacity:1" d="M 10.22325,32.383181 H 6.5405403 c -0.5689729,0 -1.027027,-0.458054 -1.027027,-1.027027 V 7.7345324 c 0,-0.568973 0.4580541,-1.027027 1.027027,-1.027027 H 41.459458 c 0.568973,0 1.027027,0.458054 1.027027,1.027027 V 31.356154 c 0,0.568973 -0.458054,1.027027 -1.027027,1.027027 H 26.256032"/><circle style="font-variation-settings:normal;opacity:1;vector-effect:none;fill:none;fill-opacity:1;fill-rule:evenodd;stroke:#000000;stroke-width:1;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;-inkscape-stroke:none;stop-color:#000000;stop-opacity:1" id="path652" cx="18.394476" cy="20.831093" r="6.1621618"/><path style="font-variation-settings:normal;opacity:1;vector-effect:none;fill:none;fill-opacity:1;stroke:#000000;stroke-width:1;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;-inkscape-stroke:none;stop-color:#000000;stop-opacity:1" d="M 8.0639084,41.292493 H 28.479018 c 0.995798,-17.147562 -21.246154,-17.87917 -20.4151096,0 z" id="path654"/><path style="font-variation-settings:normal;opacity:1;vector-effect:none;fill:none;fill-opacity:1;stroke:#000000;stroke-width:1;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;-inkscape-stroke:none;stop-color:#000000;stop-opacity:1" d="M 17.623364,6.7075054 V 14.71531" id="path657"/><path style="font-variation-settings:normal;opacity:1;vector-effect:none;fill:none;fill-opacity:1;stroke:#000000;stroke-width:1;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;-inkscape-stroke:none;stop-color:#000000;stop-opacity:1" d="M 29.705776,6.7075054 V 32.383181" id="path659"/><path style="font-variation-settings:normal;opacity:1;vector-effect:none;fill:none;fill-opacity:1;stroke:#000000;stroke-width:1;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;-inkscape-stroke:none;stop-color:#000000;stop-opacity:1" d="M 42.486485,19.545344 H 24.428401" id="path661"/><path style="font-variation-settings:normal;opacity:1;vector-effect:none;fill:none;fill-opacity:1;stroke:#000000;stroke-width:1;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;-inkscape-stroke:none;stop-color:#000000;stop-opacity:1" d="M 5.5135133,19.545344 H 12.299697" id="path663"/><path style="font-variation-settings:normal;opacity:1;vector-effect:none;fill:none;fill-opacity:1;stroke:#000000;stroke-width:1;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;-inkscape-stroke:none;stop-color:#000000;stop-opacity:1" d="M 29.774819,25.854276 H 42.486485" id="path665"/></svg>
                </span>
            </div>
        </div>
        <div class="col-6">
            <div class="wrap-input">
                <input type="text" name="business-address" class="input business-address" placeholder="Business address" value="" required />
                <span class="focus-input"></span>
                <span class="symbol-input">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="20 19 60 60"><path d="M49,18.92A23.74,23.74,0,0,0,25.27,42.77c0,16.48,17,31.59,22.23,35.59a2.45,2.45,0,0,0,3.12,0c5.24-4.12,22.1-19.11,22.1-35.59A23.74,23.74,0,0,0,49,18.92Zm0,33.71a10,10,0,1,1,10-10A10,10,0,0,1,49,52.63Z"/></svg>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <input type="file" name="your-selfie" class="form-control" placeholder="Selfie for identify your document" value="" required />
            <input type="file" name="identity-document" class="form-control" placeholder="Identity document" value="" required />
        </div>
    </div>`
)


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

		const close = renderer('button', ['close', 'btn', 'btn-primary'], { type: 'button' })
		close.textContent = 'Close'

		const send = renderer('button', ['send', 'btn', 'btn-primary'], { type: 'button' })
		send.textContent = 'Send'

		asta_modal(
			(content) => {

				const div = document.createElement('div')
				div.classList.add('container-fluid')
				div.innerHTML = vendor_form_content

				content.append(div)
			},
			(actions) => {

				send.addEventListener('click', ev => { console.log(actions.closest('.asta-modal-overlay').querySelector('[name="full-name"]').value) }, false)
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