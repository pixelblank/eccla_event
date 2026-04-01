<?php
/**
 * Template: Liste des événements futurs
 * Variables disponibles : $query, $paged, $total_pages
 */
if (!defined('ABSPATH')) exit;

if ($query->have_posts()) : ?>
    <div class="eccla-upcoming-list">
        <?php while ($query->have_posts()) : $query->the_post(); 
            $date = get_post_meta(get_the_ID(), '_event_date', true);
            $formatted_date = date_i18n('d F Y', strtotime($date));
        ?>
            <div class="eccla-event-item">
                <div>
                    <span class="event-date-label"><?php echo $formatted_date; ?></span>
                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                </div>
                <div class="event-arrow">
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if ($total_pages > 1) : ?>
        <div class="eccla-pagination">
            <?php echo paginate_links([
                'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                'format'    => '?paged=%#%',
                'current'   => $paged,
                'total'     => $total_pages,
                'prev_text' => '« Précédent',
                'next_text' => 'Suivant »',
                'type'      => 'list'
            ]); ?>
        </div>
    <?php endif; ?>

<?php else : ?>
    <p>Aucun événement prévu pour le moment.</p>
<?php endif; ?>
