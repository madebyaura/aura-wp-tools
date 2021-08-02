<?php
/**
 * Breadcrumbs.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 */

namespace MadeByAura\WPTools;

defined( 'ABSPATH' ) || die();

/**
 * Breadcrumbs.
 */
class Breadcrumbs {
	/**
	 * Breadcrumbs.
	 *
	 * @var array  Breadcrumbs.
	 */
	protected $crumbs = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->set_crumbs();
	}

	/**
	 * Render crumbs.
	 */
	public function render() {
		$crumbs = $this->crumbs;
		$last   = count( $crumbs ) - 1;
		?>
			<div class="aura-breadcrumbs">
				<?php foreach ( $crumbs as $index => $crumb ) : ?>
					<div class="aura-breadcrumbs__item">
						<?php if ( $index !== $last ) : ?>
							<a class="aura-breadcrumbs__item-link" href="<?php echo esc_url( $crumb['url'] ); ?>">
						<?php endif; ?>

						<?php echo esc_html( $crumb['text'] ); ?>

						<?php if ( $index !== $last ) : ?>
							</a><!-- .aura-breadcrumbs__item-link -->
						<?php endif; ?>
					</div><!-- .aura-breadcrumbs__item -->
				<?php endforeach; ?>
			</div><!-- .aura-breadcrumbs -->
		<?php
	}

	/**
	 * Set crumbs.
	 */
	protected function set_crumbs() {
		if ( is_front_page() ) {
			$this->add_home();

		} elseif ( is_home() ) {
			$this->add_home();
			$this->add_post_ancestors();
			$this->add_posts_page();

		} elseif ( is_singular( 'post' ) ) {
			$this->add_home();
			$this->add_posts_page();
			$this->add_post();

		} elseif ( is_singular() ) {
			$this->add_home();
			$this->add_post_ancestors();
			$this->add_post();

		} elseif ( is_category() || is_tag() ) {
			$this->add_home();
			$this->add_posts_page();
			$this->add_term_ancestors();
			$this->add_term();

		} elseif ( is_tax() ) {
			$this->add_home();
			$this->add_term_ancestors();
			$this->add_term();
		}
	}

	/**
	 * Add home.
	 */
	protected function add_home() {
		$text = __( 'Home', 'aura-wp-tools' );
		$text = apply_filters( 'aura_wp_tools_breadcrumbs_home_text', $text );

		$this->crumbs[] = [
			'text' => esc_html( $text ),
			'url'  => site_url(),
		];
	}

	/**
	 * Add post.
	 */
	protected function add_post() {
		$this->crumbs[] = [
			'text' => get_the_title(),
			'url'  => get_permalink(),
		];
	}

	/**
	 * Add post ancestors.
	 */
	protected function add_post_ancestors() {
		$post      = get_queried_object();
		$ancestors = get_ancestors( $post->ID, $post->post_type );

		// Do not proceed if there are no ancestors.
		if ( ! $ancestors ) {
			return;
		}

		// Add crumbs for post ancestors.
		foreach ( array_reverse( $ancestors ) as $ancestor ) {
			$this->crumbs[] = [
				'text' => get_the_title( $ancestor ),
				'url'  => get_permalink( $ancestor ),
			];
		}
	}

	/**
	 * Add post page.
	 */
	protected function add_posts_page() {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$this->crumbs[] = [
				'text' => get_the_title( get_option( 'page_for_posts' ) ),
				'url'  => get_permalink( get_option( 'page_for_posts' ) ),
			];
		} else {
			$this->crumbs[] = [
				'text' => get_post_type_object( 'post' )->label,
				'url'  => home_url(),
			];
		}
	}

	/**
	 * Add term.
	 */
	protected function add_term() {
		$term = get_queried_object();

		$this->crumbs[] = [
			'text' => $term->name,
			'url'  => get_term_link( $term->term_id, $term->taxonomy ),
		];
	}

	/**
	 * Add term ancestors.
	 */
	protected function add_term_ancestors() {
		$term      = get_queried_object();
		$ancestors = get_ancestors( $term->term_id, $term->taxonomy );

		// Do not proceed if there are no ancestors.
		if ( ! $ancestors ) {
			return;
		}

		// Add crumbs for term ancestor.
		foreach ( array_reverse( $ancestors ) as $ancestor ) {
			$this->crumbs[] = [
				'text' => get_term( $ancestor, $term->taxonomy )->name,
				'url'  => get_term_link( $ancestor, $term->taxonomy ),
			];
		}
	}
}
