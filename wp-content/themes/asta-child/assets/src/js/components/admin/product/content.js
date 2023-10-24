import EditorJS from '@editorjs/editorjs'
import Header from '@editorjs/header'
import List from '@editorjs/list'
import ImageTool from '@editorjs/image'
import { asta_alert } from '../../../utils/asta_alert'

const save_product = document.querySelector('.sidebar .save')

const asta_title = document.querySelector('input[name="asta-title"]')
const price = document.querySelector('input[name="price"]')
const aditional_info = document.querySelector('textarea[name="aditional-info"]')

export class Content {

    constructor(product_id, auction_json, simple_post_req, args) {

        this.product_id = product_id
        this.auction_json = auction_json
        this.json_url = args.json_url

        this.simple_post_req = simple_post_req

        this.editor = this.editor_setup()
        
        if (save_product && price && aditional_info) {
            save_product.addEventListener('click', ev => salve_product_info(), false)
        }
    }


    editor_setup() {
        return new EditorJS({
            /** 
             * Id of Element that should contain the Editor 
             */
            holder: 'editorjs',

            data: { blocks: this.auction_json },

            placeholder: 'Click here to add a full description',

            /** 
             * Available Tools list. 
             * Pass Tool's class or Settings object for each Tool you want to use 
             */
            tools: {
                header: {
                    class: Header,
                    inlineToolbar: ['link']
                },
                list: {
                    class: List,
                    inlineToolbar: true,
                    config: {
                        defaultStyle: 'unordered'
                    }
                },
                image: {
                    class: ImageTool,
                    config: {
                        endpoints: {
                            byFile: this.json_url + 'api-auction-upload-image',
                        }
                    }
                },
            },
        })
    }


    salve_product_info() {

        this.editor.save().then((editor_data) => {

            const data = {
                product_id: this.product_id,
                product_title: asta_title.value,
                price: price.value,
                aditional_info: aditional_info.value,
                product_content: editor_data.blocks
            };

            this.simple_post_req(
                'api-save-product',
                data,
                (res) => {
                    if ('success' === res.status) {
                        asta_alert([res.message], 'success')

                        if (!document.body.classList.contains('page-template-edit-product')) {
                            window.location.replace(`/edit-shop/?product_id=${product_id}`)
                        }
                    } else {
                        asta_alert([res.message])
                    }
                },
                (e) => console.log(e)
            );

        }).catch((error) => {
            console.log('Saving failed: ', error)
        });
    }
}
