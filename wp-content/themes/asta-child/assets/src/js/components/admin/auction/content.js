import EditorJS from '@editorjs/editorjs'
import Header from '@editorjs/header'
import List from '@editorjs/list'
import ImageTool from '@editorjs/image'
import { asta_alert } from '../../../utils/asta_alert'

const save_auction = document.querySelector('.sidebar .save')

const auction_title = document.querySelector('input[name="asta-title"]')
const auction_date = document.querySelector('input[name="auction-date"]')
const price = document.querySelector('input[name="price"]')
const price_increment = document.querySelector('input[name="price-increment"]')
const auction_type_select = document.querySelector('.wrap-input.select select[name="category"]')
const aditional_info = document.querySelector('textarea[name="aditional-info"]')

export class Content {

    constructor(auction_id, auction_json, simple_post_req, args) {

        this.auction_id = auction_id
        this.auction_json = auction_json
        this.json_url = args.json_url

        this.simple_post_req = simple_post_req

        this.editor = this.editor_setup(EditorJS, Header, List, ImageTool)

        if (save_auction && auction_date && price && price_increment && auction_type_select && aditional_info) {
            save_auction.addEventListener('click', ev => this.salve_auction_info(), false)
        }
    }


    editor_setup(EditorJS, Header, List, ImageTool) {
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


    salve_auction_info() {

        const type_select = auction_type_select.parentNode.querySelector('input.select');

        this.editor.save().then((editor_data) => {

            const data = {
                auction_id: this.auction_id,
                auction_title: auction_title.value,
                auction_date: auction_date.value,
                price: price.value,
                price_increment: price_increment.value,
                auction_type_select: type_select.value,
                auction_type_select_id: type_select.getAttribute('content'),
                aditional_info: aditional_info.value,
                auction_content: editor_data.blocks
            };

            this.simple_post_req(
                'api-save-auction',
                data,
                (res) => {
                    if ('success' === res.status) {
                        asta_alert([res.message], 'success')

                        if (!document.body.classList.contains('page-template-edit-auction')) {
                            window.location.replace(`/edit-auction/?auction_id=${auction_id}`)
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
