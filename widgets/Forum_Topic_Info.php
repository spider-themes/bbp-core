<?php
namespace BBPCore\WpWidgets;
use WP_Widget;
use WP_Query;

// Creating the widget
class Forum_Topic_Info extends WP_Widget {
	function __construct() {
		parent::__construct(
			'bbpress_forum_topic_info_widget', // Base ID of your widget
			__( '(BBPC) Forum Topic Info', 'bbp-core' ), // Widget name
			array( 'description' => __( 'Displays information about a bbPress forum topic', 'bbp-core' ), ) // Widget description
		);
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		global $post;

		if ( ! $post || ! isset( $post->ID ) || $post->post_type != 'topic' ) {
			return;
		}

		$topic_id = $post->ID;
		$topic = get_post( $topic_id );

		if ( empty( $topic ) || $topic->post_type != 'topic' ) {
			return;
		}

		$forum_id = get_post_meta( $topic_id, '_bbp_forum_id', true );
		$forum_url = get_permalink( $forum_id );
		$forum_title = get_the_title( $forum_id );

		$status = bbp_get_topic_status( $topic_id );
		$replies = bbp_get_topic_reply_count( $topic_id );
		$voices = bbp_get_topic_voice_count( $topic_id );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		echo '<ul>';
		echo '<li><strong>' . esc_html__( 'Forum:', 'bbp-core' ) . '</strong> <a href="' . esc_url($forum_url) . '">' . esc_html($forum_title) . '</a></li>';
		echo '<li><strong>' . esc_html__( 'Topic:', 'bbp-core' ) . '</strong> ' . esc_html($topic->post_title) . '</li>';
		echo '<li><strong>' . esc_html__( 'Author:', 'bbp-core' ) . '</strong> ' . esc_html(get_the_author_meta( 'display_name', $topic->post_author )) . '</li>';
		echo '<li><strong>' . esc_html__( 'Date:', 'bbp-core' ) . '</strong> ' . esc_html(get_the_date( '', $topic->ID )) . '</li>';
		echo '<li><strong>' . esc_html__( 'Status:', 'bbp-core' ) . '</strong> ' . esc_html($status) . '</li>';
		echo '<li><strong>' . esc_html__( 'Replies:', 'bbp-core' ) . '</strong> ' . esc_html($replies) . '</li>';
		echo '<li><strong>' . esc_html__( 'Voices:', 'bbp-core' ) . '</strong> ' . esc_html($voices) . '</li>';
		echo '</ul>';

		echo $args['after_widget'];
	}

	// Widget Backend
	public function form( $instance ) {
		$title = $instance['title'] ?? '';
		$topic_id = $instance['topic_id'] ?? '';

		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bbp-core' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'topic_id' ); ?>"><?php _e( 'Topic ID:', 'bbp-core' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'topic_id' ); ?>" name="<?php echo $this->get_field_name( 'topic_id' ); ?>" type="text" value="<?php echo esc_attr( $topic_id ); ?>" />
		</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['topic_id'] = isset( $new_instance['topic_id'] ) ? absint( $new_instance['topic_id'] ) : '';

		return $instance;
	}
}
