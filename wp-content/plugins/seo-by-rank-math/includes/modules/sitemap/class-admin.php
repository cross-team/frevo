<?php
/**
 * The Sitemap Module
 *
 * @since      0.9.0
 * @package    RankMath
 * @subpackage RankMath\Sitemap
 * @author     Rank Math <support@rankmath.com>
 */

namespace RankMath\Sitemap;

use RankMath\KB;
use RankMath\Helper;
use RankMath\Module\Base;
use RankMath\Admin\Options;
use MyThemeShop\Helpers\Str;
use MyThemeShop\Helpers\Param;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
class Admin extends Base {

	/**
	 * The Constructor.
	 */
	public function __construct() {

		$directory = dirname( __FILE__ );
		$this->config(
			[
				'id'        => 'sitemap',
				'directory' => $directory,
			]
		);
		parent::__construct();

		$this->action( 'init', 'register_setting_page', 999 );
		$this->filter( 'rank_math/settings/sitemap', 'post_type_settings' );
		$this->filter( 'rank_math/settings/sitemap', 'taxonomy_settings' );

		// Attachment.
		$this->filter( 'media_send_to_editor', 'media_popup_html', 10, 2 );
		$this->filter( 'attachment_fields_to_edit', 'media_popup_fields', 20, 2 );
		$this->filter( 'attachment_fields_to_save', 'media_popup_fields_save', 20, 2 );
	}

	/**
	 * Register setting page.
	 */
	public function register_setting_page() {
		$sitemap_url = Router::get_base_url( 'sitemap_index.xml' );

		$tabs = [
			'general' => [
				'icon'      => 'rm-icon rm-icon-settings',
				'title'     => esc_html__( 'General', 'rank-math' ),
				'file'      => $this->directory . '/settings/general.php',
				'desc'      => esc_html__( 'This tab contains General settings related to the XML sitemaps.', 'rank-math' ) . ' <a href="' . KB::get( 'sitemap-general' ) . '" target="_blank">' . esc_html__( 'Learn more', 'rank-math' ) . '</a>',
				/* translators: sitemap url */
				'after_row' => $this->get_notice_start() . sprintf( esc_html__( 'Your sitemap index can be found here: %s', 'rank-math' ), '<a href="' . $sitemap_url . '" target="_blank">' . $sitemap_url . '</a>' ) . '</p></div>' . $this->get_nginx_notice(),
			],
		];

		if ( Helper::is_author_archive_indexable() ) {
			$tabs['authors'] = [
				'icon'  => 'rm-icon rm-icon-users',
				'title' => esc_html__( 'Authors', 'rank-math' ),
				/* translators: Learn more link. */
				'desc'  => sprintf( esc_html__( 'Set the sitemap options for author archive pages. %s.', 'rank-math' ), '<a href="https://s.rankmath.com/sitemaps" target="_blank">' . esc_html__( 'Learn more', 'rank-math' ) . '</a>' ),
				'file'  => $this->directory . '/settings/authors.php',
			];
		}

		$tabs = $this->do_filter( 'settings/sitemap', $tabs );

		new Options(
			[
				'key'        => 'rank-math-options-sitemap',
				'title'      => esc_html__( 'Sitemap Settings', 'rank-math' ),
				'menu_title' => esc_html__( 'Sitemap Settings', 'rank-math' ),
				'capability' => 'rank_math_sitemap',
				'folder'     => 'titles',
				'position'   => 99,
				'tabs'       => $tabs,
			]
		);
	}

	/**
	 * Add post type tabs into sitemap option panel
	 *
	 * @param array $tabs Hold tabs for optional panel.
	 *
	 * @return array
	 */
	public function post_type_settings( $tabs ) {
		$icons  = Helper::choices_post_type_icons();
		$things = [
			'attachment' => esc_html__( 'attachments', 'rank-math' ),
			'product'    => esc_html__( 'your product pages', 'rank-math' ),
		];
		$urls   = [
			'attachment' => KB::get( 'sitemap-media' ),
			'product'    => KB::get( 'sitemap-product' ),
		];

		// Post type label seprator.
		$tabs['p_types'] = [
			'title' => esc_html__( 'Post Types:', 'rank-math' ),
			'type'  => 'seprator',
		];

		foreach ( Helper::get_accessible_post_types() as $post_type ) {
			$object      = get_post_type_object( $post_type );
			$sitemap_url = Router::get_base_url( $object->name . '-sitemap.xml' );
			$notice_end  = '</p><div class="rank-math-cmb-dependency hidden" data-relation="or"><span class="hidden" data-field="pt_' . $post_type . '_sitemap" data-comparison="=" data-value="on"></span></div></div>';
			$name        = strtolower( $object->label );

			/* translators: Post Type label */
			$thing = isset( $things[ $post_type ] ) ? $things[ $post_type ] : sprintf( __( 'single %s', 'rank-math' ), $name );
			$url   = isset( $urls[ $post_type ] ) ? $urls[ $post_type ] : ( in_array( $name, [ 'post', 'page' ], true ) ? KB::get( "sitemap-{$name}" ) : '' );

			$tabs[ 'sitemap-post-type-' . $object->name ] = [
				'title'     => 'attachment' === $post_type ? esc_html__( 'Attachments', 'rank-math' ) : $object->label,
				'icon'      => isset( $icons[ $object->name ] ) ? $icons[ $object->name ] : $icons['default'],
				/* translators: %1$s: thing, %2$s: Learn more link. */
				'desc'      => sprintf( esc_html__( 'Change Sitemap settings of %1$s. %2$s.', 'rank-math' ), $thing, '<a href="' . $url . '" target="_blank">' . esc_html__( 'Learn more', 'rank-math' ) . '</a>' ),
				'post_type' => $object->name,
				'file'      => $this->directory . '/settings/post-types.php',
				/* translators: Post Type Sitemap Url */
				'after_row' => $this->get_notice_start() . sprintf( esc_html__( 'Sitemap URL: %s', 'rank-math' ), '<a href="' . $sitemap_url . '" target="_blank">' . $sitemap_url . '</a>' ) . $notice_end,
			];

			if ( 'attachment' === $post_type ) {
				$tabs[ 'sitemap-post-type-' . $object->name ]['after_row'] = $this->get_notice_start() . esc_html__( 'Please note that this will add the attachment page URLs to the sitemap, not direct image URLs.', 'rank-math' ) . $notice_end;
				$tabs[ 'sitemap-post-type-' . $object->name ]['classes']   = 'rank-math-advanced-option';
			}
		}

		return $tabs;
	}

	/**
	 * Add taxonomy tabs into sitemap option panel
	 *
	 * @param array $tabs Hold tabs for optional panel.
	 *
	 * @return array
	 */
	public function taxonomy_settings( $tabs ) {
		$icons = Helper::choices_taxonomy_icons();

		// Taxonomy label seprator.
		$tabs['t_types'] = [
			'title' => esc_html__( 'Taxonomies:', 'rank-math' ),
			'type'  => 'seprator',
		];

		foreach ( Helper::get_accessible_taxonomies() as $taxonomy ) {
			if ( 'post_format' === $taxonomy->name ) {
				continue;
			}
			$sitemap_url = Router::get_base_url( $taxonomy->name . '-sitemap.xml' );
			$notice_end  = '</p><div class="rank-math-cmb-dependency hidden" data-relation="or"><span class="hidden" data-field="tax_' . $taxonomy->name . '_sitemap" data-comparison="=" data-value="on"></span></div></div>';

			switch ( $taxonomy->name ) {
				case 'product_cat':
				case 'product_tag':
					/* translators: Taxonomy singular label */
					$thing = sprintf( __( 'your product %s pages', 'rank-math' ), strtolower( $taxonomy->labels->singular_name ) );
					$url   = KB::get( "sitemap-$taxonomy->name" );
					break;

				default:
					/* translators: Taxonomy singular label */
					$thing = sprintf( __( '%s archives', 'rank-math' ), strtolower( $taxonomy->labels->singular_name ) );
					$name  = strtolower( $taxonomy->labels->name );
					$url   = in_array( $name, [ 'category', 'tags' ], true ) ? KB::get( "sitemap-{$name}" ) : '';
			}

			$tabs[ 'sitemap-taxonomy-' . $taxonomy->name ] = [
				'icon'      => isset( $icons[ $taxonomy->name ] ) ? $icons[ $taxonomy->name ] : $icons['default'],
				'title'     => $taxonomy->label,
				/* translators: %1$s: thing, %2$s: Learn more link. */
				'desc'      => sprintf( esc_html__( 'Change Sitemap settings of %1$s. %2$s.', 'rank-math' ), $thing, '<a href="' . $url . '" target="_blank">' . esc_html__( 'Learn more', 'rank-math' ) . '</a>' ),
				'taxonomy'  => $taxonomy->name,
				'file'      => $this->directory . '/settings/taxonomies.php',
				/* translators: Taxonomy Sitemap Url */
				'after_row' => $this->get_notice_start() . sprintf( esc_html__( 'Sitemap URL: %s', 'rank-math' ), '<a href="' . $sitemap_url . '" target="_blank">' . $sitemap_url . '</a>' ) . $notice_end,
			];
		}

		return $tabs;
	}

	/**
	 * Adds new "exclude from sitemap" checkbox to media popup in the post editor.
	 *
	 * @param array  $form_fields Default form fields.
	 * @param object $post        Current post.
	 *
	 * @return array New form fields
	 */
	public function media_popup_fields( $form_fields, $post ) {
		$exclude   = get_post_meta( $post->ID, 'rank_math_exclude_sitemap', true );
		$checkbox  = '<label><input type="checkbox" name="attachments[' . $post->ID . '][rank_math_media_exclude_sitemap]" ' . checked( $exclude, true, 0 ) . ' /> ';
		$checkbox .= esc_html__( 'Exclude this image from sitemap', 'rank-math' ) . '</label>';

		$form_fields['rank_math_exclude_sitemap'] = [ 'tr' => "\t\t<tr><td></td><td>$checkbox</td></tr>\n" ];

		return $form_fields;
	}

	/**
	 * Saves new "exclude from sitemap" field as post meta to attachment.
	 *
	 * @param array $post       Attachment ID.
	 * @param array $attachment Attachment data.
	 *
	 * @return array Post
	 */
	public function media_popup_fields_save( $post, $attachment ) {

		if ( isset( $attachment['rank_math_media_exclude_sitemap'] ) ) {
			update_post_meta( $post['ID'], 'rank_math_exclude_sitemap', true );
		} else {
			delete_post_meta( $post['ID'], 'rank_math_exclude_sitemap' );
		}

		Cache_Watcher::invalidate_post( $post['ID'] );

		return $post;
	}

	/**
	 * Adds html attribute data-sitemapexclude to img tag in the post editor
	 * when necessary.
	 *
	 * @param string $html          Original img HTML tag.
	 * @param int    $attachment_id Attachment ID.
	 *
	 * @return string New img HTML tag.
	 */
	public function media_popup_html( $html, $attachment_id ) {
		$post = get_post( $attachment_id );
		if ( Str::starts_with( 'image', $post->post_mime_type ) && get_post_meta( $attachment_id, 'rank_math_exclude_sitemap', true ) ) {
			$html = str_replace( ' class="', ' data-sitemapexclude="true" class="', $html );
		}
		return $html;
	}

	/**
	 * Get notice start html div
	 *
	 * @return string
	 */
	private function get_notice_start() {
		return '<div class="notice notice-alt notice-info info inline rank-math-notice"><p>';
	}

	/**
	 * Get nginx notice.
	 *
	 * @since 1.0.41
	 *
	 * @return string
	 */
	private function get_nginx_notice() {
		if ( empty( Param::server( 'SERVER_SOFTWARE' ) ) ) {
			return '';
		}

		$server_software = explode( '/', Param::server( 'SERVER_SOFTWARE' ) );
		if ( ! in_array( 'nginx', array_map( 'strtolower', $server_software ), true ) ) {
			return '';
		}

		$sitemap_base = Router::get_sitemap_base() ? Router::get_sitemap_base() : '';

		/* translators: sitemap base url */
		return '<div class="sitemap-nginx-notice notice notice-alt notice-warning rank-math-notice">
		 <p>' . sprintf( __( 'Since you are using NGINX, add this code to your NGINX %s <strong>if your Sitemap pages are not loading</strong> or you can ask your hosting support to add it.', 'rank-math' ), '<a href="https://help.dreamhost.com/hc/en-us/articles/216455077-Nginx-configuration-file-locations/?utm_campaign=Rank+Math" target="_blank">' . __( 'configuration file', 'rank-math' ) . '</a>' ) . '
		 <a href="#"><span class="show">' . __( 'Click here to see the code.', 'rank-math' ) . '</span><span class="hide">' . __( 'Hide', 'rank-math' ) . '</span></a></p>
 <pre>
 # START Nginx Rewrites for Rank Math Sitemaps
 rewrite ^/' . $sitemap_base . 'sitemap_index.xml$ /index.php?sitemap=1 last;
 rewrite ^/' . $sitemap_base . '([^/]+?)-sitemap([0-9]+)?.xml$ /index.php?sitemap=$1&sitemap_n=$2 last;
 # END Nginx Rewrites for Rank Math Sitemaps
 </pre>
		 </div>';
	}
}
