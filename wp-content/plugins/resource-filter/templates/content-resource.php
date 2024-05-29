<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="post-thumbnail">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail(); ?>
                </a>
            </div>
        <?php endif; ?>
        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    </header>

    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div>

    <footer class="entry-footer">
        <?php echo get_the_term_list( get_the_ID(), 'resource_type', 'Type: ', ', ' ); ?>
        <?php echo get_the_term_list( get_the_ID(), 'resource_topic', 'Topic: ', ', ' ); ?>
    </footer>
</article>
