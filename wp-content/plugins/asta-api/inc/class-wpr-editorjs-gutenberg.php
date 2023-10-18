<?php

class WPR_EditorJS_Gutenberg {


	private array $editorjs_content;


	public function __construct( array $editorjs_content ) {
		$this->editorjs_content = $editorjs_content;
	}


	/**
	 * This PHP function replaces all instances of `<b>` and `</b>` with `<strong>` and `</strong>`
	 * respectively in a given string.
	 *
	 * @param string text The parameter "text" is a string variable that represents the text to be
	 * modified by the function.
	 *
	 * @return string function `adapter_new_tags` is returning a modified string where all occurrences of
	 * `<b>` and `</b>` are replaced with `<strong>` and `</strong>` respectively.
	 */
	private function adapter_new_tags( string $text ) {
		return str_replace( array( '<b>', '</b>' ), array( '<strong>', '</strong>' ), $text );
	}


	/**
	 * This is a PHP function that renders a headline with a specified level and text.
	 *
	 * @param array data The parameter `` is an array that contains the following keys:
	 *
	 * @return string that contains an HTML heading element with the level and text specified in the
	 * input array. The text is passed through a method called `adapter_new_tags` before being included in
	 * the HTML output.
	 */
	private function render_headline( array $data ) {
		$content = sprintf(
			'<!-- wp:heading {"level":%s} --><h%d class="wp-block-heading">%s</h%d><!-- /wp:heading -->',
			$data['level'],
			$data['level'],
			$this->adapter_new_tags( $data['text'] ),
			$data['level'],
		);

		return $content;
	}


	/**
	 * The function renders a paragraph block in WordPress using the provided text data.
	 *
	 * @param array data  is an array parameter that contains the data needed to render a paragraph.
	 * It should have a 'text' key that contains the text content of the paragraph.
	 *
	 * @return string that contains an HTML paragraph block with the text content passed in the ``
	 * array. The text content is sanitized using the `adapter_new_tags` method before being inserted into
	 * the paragraph block.
	 */
	private function render_paragraph( array $data ) {
		return sprintf(
			'<!-- wp:paragraph --><p>%s</p><!-- /wp:paragraph -->',
			$this->adapter_new_tags( $data['text'] ),
		);
	}


	/**
	 * This is a PHP function that renders an HTML list of items passed as an array.
	 *
	 * @param array items The parameter `` is an array of items that will be used to generate an
	 * HTML list. Each item in the array will be wrapped in an HTML list item (`<li>`) tag.
	 *
	 * @return string of HTML code that contains a list of items. The items are passed as an array to
	 * the function and are iterated over using a foreach loop. Each item is then formatted as a list item
	 * using the sprintf function and added to the  string. Finally, the  string is
	 * returned.
	 */
	private function render_item_list( array $items ) {

		$items_html = '';

		foreach ( $items as $item ) {
			$items_html .= sprintf(
				'<!-- wp:list-item --><li>%s</li><!-- /wp:list-item -->',
				$this->adapter_new_tags( $item )
			);
		}

		return $items_html;
	}


	/**
	 * The function renders a list in HTML format based on the data provided.
	 *
	 * @param array data The  parameter is an array that contains information about the list to be
	 * rendered, including the style of the list (unordered or ordered) and the items to be included in
	 * the list.
	 *
	 * @return string that contains an HTML list element (`ul` or `ol`) with the items passed in the
	 * `` array rendered as list items (`li`). The type of list element is determined by the value of
	 * the `['style']` parameter. If it is set to `'unordered'`, an unordered list (`ul`) is
	 * returned, otherwise an ordered list (`ol
	 */
	private function render_list( array $data ) {

		$list_type = 'unordered' === $data['style'] ? 'ul' : 'ol';

		return sprintf(
			'<!-- wp:list %s--><%s>%s</%s><!-- /wp:list -->',
			( 'ol' === $list_type ? '{"ordered":true} ' : '' ),
			$list_type,
			$this->render_item_list( $data['items'] ),
			$list_type
		);
	}


	/**
	 * The function renders content from an editor in Gutenberg format into HTML.
	 *
	 * @return string rendered content as a string, which has been sanitized using the `esc_sql()` function.
	 */
	public function render_gutenberg() {

		$content = '';

		foreach ( $this->editorjs_content as $element ) {

			switch ( $element['type'] ) {
				case 'header':
					$content .= $this->render_headline( $element['data'] );
					break;
				case 'paragraph':
					$content .= $this->render_paragraph( $element['data'] );
					break;
				case 'list':
					$content .= $this->render_list( $element['data'] );
					break;
			}
		}

		return esc_sql( $content );
	}
}
