<?php
/**
 * Template: Liste des événements futurs (Verdant Shore Design)
 */
if (!defined('ABSPATH')) exit;

if ($query->have_posts()) : ?>
    <div class="eccla-upcoming-list">
        <?php while ($query->have_posts()) : $query->the_post(); 
            $id = get_the_ID();
            $date = get_post_meta($id, '_event_date', true);
            $color = get_post_meta($id, '_event_color', true) ?: '#53a92c';
            $text_color = eccla_get_contrast_color($color);
            $formatted_date = date_i18n('d F', strtotime($date));
        ?>
            <div class="eccla-event-item group">
                <div class="flex justify-between items-start w-full mb-4">
                    <span class="event-date-label" style="background: <?php echo esc_attr($color); ?>; color: <?php echo esc_attr($text_color); ?>;"><?php echo $formatted_date; ?></span>
                    <span class="dashicons dashicons-calendar-alt" style="color: #d6d3d1;"></span>
                </div>
                
                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                
                <a href="<?php the_permalink(); ?>" class="event-details-btn">
                    Détails
                </a>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if ($total_pages > 1) : ?>
        <div class="eccla-pagination" style="margin-top: 40px;">
            <?php echo paginate_links([
                'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                'format'    => '?paged=%#%',
                'current'   => $paged,
                'total'     => $total_pages,
                'prev_text' => '«',
                'next_text' => '»',
                'type'      => 'plain'
            ]); ?>
        </div>
    <?php endif; ?>

<?php else : ?>
    <p>Aucun événement prévu pour le moment.</p>
<?php endif; ?>
